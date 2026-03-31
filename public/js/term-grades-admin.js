// JS to override the form action for admin only
window.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('addGradeForm');
    if (form && window.location.pathname.startsWith('/admin/')) {
        form.action = '/admin/term_grades';
    }
});