#!/usr/bin/env python3
"""Convert MySQL (phpMyAdmin) dump to PostgreSQL-compatible SQL - robust version."""

import re
import sys


def convert_mysql_to_pg(input_file, output_file):
    with open(input_file, 'r', encoding='utf-8', errors='replace') as f:
        content = f.read()

    # ---- Pre-processing: fix MySQL-specific syntax globally ----

    # Remove MySQL conditional comments /*!...*/ but keep content inside
    content = re.sub(r'/\*!\d+\s*', '', content)
    content = re.sub(r'\s*\*/', '', content)

    # Remove SET SQL_MODE, SET time_zone, LOCK/UNLOCK
    content = re.sub(r"^SET SQL_MODE\s*=.*?;\s*$", '', content, flags=re.MULTILINE)
    content = re.sub(r"^SET time_zone\s*=.*?;\s*$", '', content, flags=re.MULTILINE)
    content = re.sub(r"^SET NAMES\s+\w+\s*;\s*$", '', content, flags=re.MULTILINE)
    content = re.sub(r"^SET CHARACTER_SET.*?;\s*$", '', content, flags=re.MULTILINE)
    content = re.sub(r"^SET COLLATION.*?;\s*$", '', content, flags=re.MULTILINE)
    content = re.sub(r"^START TRANSACTION;\s*$", '', content, flags=re.MULTILINE)
    content = re.sub(r"^COMMIT;\s*$", '', content, flags=re.MULTILINE)
    content = re.sub(r"^LOCK TABLES.*?;\s*$", '', content, flags=re.MULTILINE)
    content = re.sub(r"^UNLOCK TABLES;\s*$", '', content, flags=re.MULTILINE)

    # ---- Process CREATE TABLE blocks ----
    create_pattern = re.compile(
        r'CREATE TABLE `(\w+)` \((.*?)\)\s*ENGINE=.*?;',
        re.DOTALL
    )

    auto_inc_info = {}  # table -> column

    def convert_create_table(match):
        table_name = match.group(1)
        body = match.group(2)

        col_lines = []
        pk_line = None

        # Split body into individual definitions (handling nested parens)
        defs = []
        current = ''
        paren_depth = 0
        in_string = False
        escape_next = False
        string_char = None

        for char in body:
            if escape_next:
                current += char
                escape_next = False
                continue
            if char == '\\':
                escape_next = True
                current += char
                continue
            if in_string:
                current += char
                if char == string_char:
                    in_string = False
                continue
            if char in ("'", '"'):
                in_string = True
                string_char = char
                current += char
                continue
            if char == '(':
                paren_depth += 1
            elif char == ')':
                paren_depth -= 1
            elif char == ',' and paren_depth == 0:
                defs.append(current.strip())
                current = ''
                continue
            current += char
        if current.strip():
            defs.append(current.strip())

        for d in defs:
            d = d.strip()
            if not d:
                continue

            # Skip KEY, INDEX, UNIQUE KEY, FULLTEXT, CONSTRAINT
            if re.match(r'(KEY|INDEX|UNIQUE KEY|FULLTEXT|CONSTRAINT)\s', d, re.IGNORECASE):
                continue

            # PRIMARY KEY
            m_pk = re.match(r'PRIMARY KEY\s*\((.+?)\)', d, re.IGNORECASE)
            if m_pk:
                keys = re.findall(r'`(\w+)`', m_pk.group(1))
                pk_line = '  PRIMARY KEY (' + ', '.join(f'"{k}"' for k in keys) + ')'
                continue

            # Column definition
            m_col = re.match(r'`(\w+)`\s+(.*)', d, re.DOTALL)
            if m_col:
                col_name = m_col.group(1)
                col_def = m_col.group(2).strip()

                # Check AUTO_INCREMENT
                if re.search(r'AUTO_INCREMENT', col_def, re.IGNORECASE):
                    auto_inc_info[table_name] = col_name

                # Remove COMMENT '...' (handle escaped quotes)
                col_def = re.sub(r"\s*COMMENT\s+'(?:[^'\\]|\\.)*'", '', col_def, flags=re.IGNORECASE)
                col_def = re.sub(r'\s*COMMENT\s+"(?:[^"\\]|\\.)*"', '', col_def, flags=re.IGNORECASE)

                # Remove AUTO_INCREMENT
                col_def = re.sub(r'\s*AUTO_INCREMENT', '', col_def, flags=re.IGNORECASE)

                # Remove UNSIGNED
                col_def = re.sub(r'\s+UNSIGNED', '', col_def, flags=re.IGNORECASE)

                # Remove CHARACTER SET / COLLATE
                col_def = re.sub(r'\s*CHARACTER SET\s+\w+', '', col_def, flags=re.IGNORECASE)
                col_def = re.sub(r'\s*COLLATE\s+\w+', '', col_def, flags=re.IGNORECASE)

                # Remove ON UPDATE CURRENT_TIMESTAMP
                col_def = re.sub(r'\s*ON UPDATE CURRENT_TIMESTAMP(\(\))?', '', col_def, flags=re.IGNORECASE)

                # ENUM -> VARCHAR(255)
                col_def = re.sub(r"enum\s*\([^)]+\)", 'VARCHAR(255)', col_def, flags=re.IGNORECASE)

                # tinyint(1) -> BOOLEAN
                col_def = re.sub(r'\btinyint\s*\(\s*1\s*\)', 'BOOLEAN', col_def, flags=re.IGNORECASE)

                # int types (order matters - bigint before int)
                col_def = re.sub(r'\bbigint\b(\s*\(\s*\d+\s*\))?', 'BIGINT', col_def, flags=re.IGNORECASE)
                col_def = re.sub(r'\bmediumint\b(\s*\(\s*\d+\s*\))?', 'INTEGER', col_def, flags=re.IGNORECASE)
                col_def = re.sub(r'\btinyint\b(\s*\(\s*\d+\s*\))?', 'SMALLINT', col_def, flags=re.IGNORECASE)
                col_def = re.sub(r'\bint\b(\s*\(\s*\d+\s*\))?', 'INTEGER', col_def, flags=re.IGNORECASE)

                # Float/Double
                col_def = re.sub(r'\bfloat\b(\s*\(\s*\d+\s*,\s*\d+\s*\))?', 'REAL', col_def, flags=re.IGNORECASE)
                col_def = re.sub(r'\bdouble\b(\s*\(\s*\d+\s*,\s*\d+\s*\))?', 'DOUBLE PRECISION', col_def, flags=re.IGNORECASE)

                # Text types
                col_def = re.sub(r'\blongtext\b', 'TEXT', col_def, flags=re.IGNORECASE)
                col_def = re.sub(r'\bmediumtext\b', 'TEXT', col_def, flags=re.IGNORECASE)
                col_def = re.sub(r'\btinytext\b', 'TEXT', col_def, flags=re.IGNORECASE)

                # Blob types
                col_def = re.sub(r'\blongblob\b', 'BYTEA', col_def, flags=re.IGNORECASE)
                col_def = re.sub(r'\bmediumblob\b', 'BYTEA', col_def, flags=re.IGNORECASE)
                col_def = re.sub(r'\btinyblob\b', 'BYTEA', col_def, flags=re.IGNORECASE)
                col_def = re.sub(r'\bblob\b(?!\w)', 'BYTEA', col_def, flags=re.IGNORECASE)

                # datetime -> TIMESTAMP
                col_def = re.sub(r'\bdatetime\b', 'TIMESTAMP', col_def, flags=re.IGNORECASE)

                # CURRENT_TIMESTAMP() -> CURRENT_TIMESTAMP
                col_def = re.sub(r'\bCURRENT_TIMESTAMP\(\)', 'CURRENT_TIMESTAMP', col_def, flags=re.IGNORECASE)

                # Boolean defaults
                if 'BOOLEAN' in col_def:
                    col_def = re.sub(r"DEFAULT\s+'0'", 'DEFAULT FALSE', col_def)
                    col_def = re.sub(r"DEFAULT\s+0\b", 'DEFAULT FALSE', col_def)
                    col_def = re.sub(r"DEFAULT\s+'1'", 'DEFAULT TRUE', col_def)
                    col_def = re.sub(r"DEFAULT\s+1\b", 'DEFAULT TRUE', col_def)

                # Clean extra spaces
                col_def = re.sub(r'\s+', ' ', col_def).strip()

                col_lines.append(f'  "{col_name}" {col_def}')

        # Build output
        all_lines = col_lines.copy()
        if pk_line:
            all_lines.append(pk_line)

        result = f'DROP TABLE IF EXISTS "{table_name}" CASCADE;\n'
        result += f'CREATE TABLE "{table_name}" (\n'
        result += ',\n'.join(all_lines)
        result += '\n);\n'

        if table_name in auto_inc_info:
            col = auto_inc_info[table_name]
            seq = f"{table_name}_{col}_seq"
            result += f'\nCREATE SEQUENCE IF NOT EXISTS "{seq}";\n'
            result += f"ALTER TABLE \"{table_name}\" ALTER COLUMN \"{col}\" SET DEFAULT nextval('\"{seq}\"');\n"
            result += f"ALTER SEQUENCE \"{seq}\" OWNED BY \"{table_name}\".\"{col}\";\n"

        return result

    content = create_pattern.sub(convert_create_table, content)

    # ---- Convert INSERT statements ----
    def convert_insert(match):
        full = match.group(0)
        vals_idx = full.upper().find(' VALUES')
        if vals_idx == -1:
            return re.sub(r'`(\w+)`', r'"\1"', full)

        header = full[:vals_idx]
        values_part = full[vals_idx:]

        header = re.sub(r'`(\w+)`', r'"\1"', header)

        # Fix boolean bit literals
        values_part = re.sub(r",b'0'", ",FALSE", values_part)
        values_part = re.sub(r"\(b'0'", "(FALSE", values_part)
        values_part = re.sub(r",b'1'", ",TRUE", values_part)
        values_part = re.sub(r"\(b'1'", "(TRUE", values_part)

        return header + values_part

    content = re.sub(
        r"INSERT INTO\s+`\w+`\s*\([^)]+\)\s*VALUES\s*.*?;",
        convert_insert,
        content,
        flags=re.DOTALL
    )

    # ---- Convert ALTER TABLE AUTO_INCREMENT ----
    def convert_alter_auto(match):
        table = match.group(1)
        auto_val = match.group(2)
        if table in auto_inc_info:
            col = auto_inc_info[table]
            seq = f"{table}_{col}_seq"
            return f"SELECT setval('\"{seq}\"', {auto_val}, true);"
        return ''

    content = re.sub(
        r"ALTER TABLE `(\w+)`\s*\n?\s*MODIFY.*?AUTO_INCREMENT=(\d+).*?;",
        convert_alter_auto,
        content,
        flags=re.DOTALL | re.IGNORECASE
    )

    # Remove remaining backtick-only ALTER TABLE
    content = re.sub(
        r"ALTER TABLE `(\w+)`\s+AUTO_INCREMENT\s*=\s*(\d+)\s*;",
        convert_alter_auto,
        content,
        flags=re.IGNORECASE
    )

    # ---- Output ----
    output = "-- Converted from MySQL to PostgreSQL\n"
    output += "SET client_encoding = 'UTF8';\n"
    output += "SET standard_conforming_strings = on;\n\n"
    output += "BEGIN;\n\n"
    output += content
    output += "\nCOMMIT;\n"

    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(output)

    tables = len(re.findall(r'CREATE TABLE', output))
    inserts = len(re.findall(r'INSERT INTO', output))
    sequences = len(re.findall(r'CREATE SEQUENCE', output))
    print(f"✅ Conversion complete: {output_file}")
    print(f"   Tables: {tables}, Inserts: {inserts}, Sequences: {sequences}")
    print(f"   Size: {len(output) / 1024 / 1024:.1f} MB")


if __name__ == '__main__':
    input_file = sys.argv[1] if len(sys.argv) > 1 else '/Users/zimamit/Downloads/nahj (1).sql'
    output_file = sys.argv[2] if len(sys.argv) > 2 else '/Users/zimamit/Downloads/nahj_pg.sql'
    convert_mysql_to_pg(input_file, output_file)
