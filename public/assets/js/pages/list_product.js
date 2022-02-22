$(document).ready(function() {
  $('#productTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: '<?= base_url(route_to('products_get')) ?>',
      type: 'GET',
    },
    "pagingType": "full_numbers",
    language: {
      url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Turkish.json"
    },
    responsive: true,
    columnDefs: [{
      searchable: false,
      targets: [0, 4, 5],
    }],
  });
});