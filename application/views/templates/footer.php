</main>
    </div>
</div>

<footer class="footer text-center">
    <div class="container" >
        <span style="color:white; font-weight:300">© <?= date('Y') ?> Koperasi. All rights reserved.</span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTables
    $('.table').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
        },
        responsive: true
    });

    // Toggle sidebar on mobile
    $('#sidebarToggle').click(function(e) {
        e.preventDefault();
        $('.sidebar').toggleClass('show');
    });

    // Hide sidebar when clicking outside on mobile
    $(document).click(function(e) {
        if (!$(e.target).closest('#sidebar, #sidebarToggle').length) {
            $('.sidebar').removeClass('show');
        }
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
<script>
$(document).ready(function() {
    // Toggle sidebar saat tombol diklik
    $('#sidebarToggle').click(function(e) {
        e.preventDefault();
        $('#sidebar').toggleClass('show');
        $('.sidebar-overlay').toggleClass('show');
    });
    
    // Sembunyikan sidebar saat mengklik overlay
    $('.sidebar-overlay').click(function() {
        $('#sidebar').removeClass('show');
        $('.sidebar-overlay').removeClass('show');
    });
    
    // Sembunyikan sidebar saat ukuran layar berubah ke desktop
    $(window).resize(function() {
        if ($(window).width() >= 992) {
            $('#sidebar').removeClass('show');
            $('.sidebar-overlay').removeClass('show');
        }
    });
});
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>