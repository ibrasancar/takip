</div>
</div>
</div>
<!-- Javascripts -->
<script src="<?= site_url('/assets/plugins/jquery/jquery-3.5.1.min.js') ?>"></script>
<script src="<?= site_url('/assets/plugins/bootstrap/js/bootstrap.min.js') ?>"></script>
<script src="<?= site_url('/assets/plugins/perfectscroll/perfect-scrollbar.min.js') ?>"></script>
<script src="<?= site_url('/assets/plugins/pace/pace.min.js') ?>"></script>
<script src="<?= site_url('/assets/js/main.min.js') ?>"></script>
<script src="<?= site_url('/assets/js/custom.js') ?>"></script>
<script src="<?= site_url('assets/plugins/sweetalert.min.js') ?>"></script>
<?= $this->renderSection('scripts') ?>

<?php if (isset($success)) : ?>
  <script>
    swal("<?= $success ?>", {
      icon: 'success',
      button: 'Tamam',
      timer: 2500,
    });
  </script>
<?php endif; ?>
<?php if (isset($error) && !is_array($error)) : ?>
  <script>
    swal("<?= $error ?>", {
      icon: 'error',
      button: 'Tamam',
      timer: 2500,
    });
  </script>
<?php elseif (isset($error) && is_array($error)) : ?>
  <script>
    swal("<?= implode('\n', $error) ?>", {
      icon: 'error',
      button: 'Tamam',
      timer: 2500,
    });
  </script>
<?php endif; ?>

<script>
  $(document).on('click', '.delete', function(e) {
    e.preventDefault();
    swal("Silmek istediğinize emin misiniz?", {
      icon: 'warning',
      buttons: {
        cancel: 'İptal Et',
        approve: 'Evet, sil!',
      }
    }).then((value) => {
      switch (value) {
        case "approve":
          window.location = $(this).attr('href');
          break;

        default:
          break;
      }
    });
  })

  $(".logo-icon").click(function(e) {
    e.preventDefault();
    a();
  })

  function a() {
    return !$(".app").hasClass("menu-off-canvas") && ($(".app").toggleClass("sidebar-hidden"),
      $(".app").hasClass("sidebar-hidden") ? (setTimeout(function() {
          $(".app-sidebar .logo").addClass("hidden-sidebar-logo")
        }, 200),
        $(window).width() > 1199 ? $(".hide-sidebar-toggle-button i").text("last_page") : $(".hide-sidebar-toggle-button i").text("first_page")) : ($(".app-sidebar .logo").removeClass("hidden-sidebar-logo"),
        $(window).width() > 1199 ? $(".hide-sidebar-toggle-button i").text("first_page") : $(".hide-sidebar-toggle-button i").text("last_page")),
      !1)
  }
</script>
</body>

</html>