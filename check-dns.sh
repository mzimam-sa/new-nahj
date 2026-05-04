#!/bin/zsh
echo "=== DNS Check for nahj.com.sa ==="
echo ""
echo "1. A Record (should be 151.101.2.15):"
dig +short nahj.com.sa A
echo ""
echo "2. Railway Verify TXT:"
dig +short _railway-verify.nahj.com.sa TXT
echo ""
echo "3. Website Response:"
RESPONSE=$(curl -sI "https://nahj.com.sa" 2>&1 | head -5)
echo "$RESPONSE"
echo ""
if echo "$RESPONSE" | grepns1.dnetns.com
ns2.dnetns.com
ns3.dnetns.comns1.dnetns.com
ns2.dnetns.com
ns3.dnetns.com -q "hostinger"; then
  echo "⏳ لسه على Hostinger — انتظر شوي"
elif echo "$RESPONSE" | grep -q "railway"; then
  echo "✅ انتقل لـ Railway!"
else
  echo "🔍 تحقق يدوي — شوف الـ headers فوق"
fi
