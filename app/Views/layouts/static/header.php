<div class="search">
  <form>
    <input class="form-control" type="text" placeholder="Arama yapın..." aria-label="Ara" />
  </form>
  <a href="#" class="toggle-search"><i class="material-icons">close</i></a>
</div>
<div class="app-header">
  <nav class="navbar navbar-light navbar-expand-lg">
    <div class="container-fluid">
      <div class="navbar-nav" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link hide-sidebar-toggle-button" href="#"><i class="material-icons">first_page</i></a>
          </li>
          <!--<li class="nav-item dropdown hidden-on-mobile">-->
          <!--  <a class="nav-link dropdown-toggle" href="#" id="addDropdownLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">-->
          <!--    <i class="material-icons">add</i>-->
          <!--  </a>-->
          <!--  <ul class="dropdown-menu" aria-labelledby="addDropdownLink">-->
          <!--    <li>-->
          <!--      <a class="dropdown-item" href="#">New Workspace</a>-->
          <!--    </li>-->
          <!--    <li><a class="dropdown-item" href="#">New Board</a></li>-->
          <!--    <li>-->
          <!--      <a class="dropdown-item" href="#">Create Project</a>-->
          <!--    </li>-->
          <!--  </ul>-->
          <!--</li>-->
        </ul>
      </div>
      <div class="d-flex">
        <ul class="navbar-nav">
          <li class="nav-item hidden-on-mobile">
            <a class="nav-link" href="<?= site_url(route_to('add_product')) ?>">Ürün Ekle</a>
          </li>
          <li class="nav-item hidden-on-mobile">
            <a class="nav-link" href="<?= site_url(route_to('add_customer')) ?>">Müşteri Ekle</a>
          </li>
          <li class="nav-item hidden-on-mobile">
            <a class="nav-link" href="<?= site_url(route_to('logout')) ?>">Çıkış Yap</a>
          </li>
          <li class="nav-item">
            <a class="nav-link toggle-search" href="#"><i class="material-icons">search</i></a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</div>
<div class="app-content">