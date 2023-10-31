<?php 

class PlugIns {

// Bootstrapt--------------------------------------------------
public function BootstrapPlugIn() {
ob_start();
?>

<!-- Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
    integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
</script>
<!-- Bootstrap -->

<?php
        return ob_get_clean();
    }
    
// Jquery--------------------------------------------------
public function jqueryPlugin() {
ob_start();
?>
<!-- JQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<!-- JQuery -->
<?php
        return ob_get_clean();
    }

// Jquery--------------------------------------------------
public function SweetAlert2Plugin() {
ob_start();
?>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- SweetAlert2 -->
<?php
        return ob_get_clean();
    }

public function BoxiconsPlugin() {
    ob_start();
?>
<!-- Boxicons -->
<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<!-- Boxicons -->
<?php
        return ob_get_clean();
    }

public function CustomAnimateScrollPlugin() {
    ob_start();
?>
<!-- Custom Animate Scroll -->
<link rel="stylesheet" href="../assets/animatescroll.css">
<script defer src="../assets/animatecsroll.js"></script>
<!-- Custom Animate Scroll -->
<?php
        return ob_get_clean();
    }

}

















?>