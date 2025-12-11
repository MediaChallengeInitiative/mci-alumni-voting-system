<!-- jQuery 3 - Minified -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 - Minified -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- iCheck 1.0.1 - For styled checkboxes -->
<script src="plugins/iCheck/icheck.min.js"></script>
<!-- AdminLTE App - Minified -->
<script src="dist/js/adminlte.min.js"></script>

<!-- Performance Optimization Script -->
<script>
(function() {
    'use strict';

    // Debounce function for performance
    function debounce(func, wait) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    }

    // Initialize on DOM ready
    $(function() {
        // Cache DOM elements
        var $content = $('.content');
        var $alerts = $('.alert');

        // Auto-hide alerts after 5 seconds
        if ($alerts.length) {
            setTimeout(function() {
                $alerts.fadeOut(300);
            }, 5000);
        }

        // Lazy load images that are off-screen
        if ('IntersectionObserver' in window) {
            var imageObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(function(img) {
                imageObserver.observe(img);
            });
        }
    });
})();
</script>