const extraInput =
  '<li><input type="text" name="extras[]" class="form-control mb-2" placeholder="Renk kartela kodu, ürün boyutu vs."></li>';

// datepickers
const order_date = $(".order_date").flatpickr({
  enableTime: true,
  time_24hr: true,
  locale: "tr",
  altInput: true,
  altFormat: "d-m-Y H:i",
  dateFormat: "Y-m-d H:i",
});
const estimated_delivery = $(".estimated_delivery").flatpickr({
  locale: "tr",
  altInput: true,
  altFormat: "d-m-Y",
  dateFormat: "Y-m-d",
});
const shipping_date = $(".shipping_date").flatpickr({
  locale: "tr",
  enableTime: true,
  altInput: true,
  altFormat: "d-m-Y H:i",
  dateFormat: "Y-m-d H:i",
});
const deliver_confirm = $(".deliver_confirm").flatpickr({
  locale: "tr",
  enableTime: true,
  time_24hr: true,
  altInput: true,
  altFormat: "d-m-Y H:i",
  dateFormat: "Y-m-d H:i",
});
const completed_at = $(".completed_at").flatpickr({
  locale: "tr",
  enableTime: true,
  time_24hr: true,
  altInput: true,
  altFormat: "d-m-Y H:i",
  dateFormat: "Y-m-d H:i",
});
function resetExtraList(empty = false) {
  $("#list_extra").html(empty == false ? extraInput : "");
}

function clearPrice(price) {
  price = price.toString();
  return price.replace(".", "").replace(",", ".").replace("₺", "");
}

function fixPrice(price) {
  return price.toString().replace(".", ",");
}

function calcSubTotal() {
  let subtotal = 0;
  $("table.invoice-table td.price").each(function (index) {
    let price = parseFloat(clearPrice($(this).html()));
    subtotal += price;
  });
  return subtotal;
}

// masks
$("#price").inputmask("decimal", {
  radixPoint: ",",
  groupSeparator: ".",
  allowMinus: false,
  suffix: " ₺",
  digits: 2,
  digitsOptional: false,
  rightAlign: true,
  unmaskAsNumber: true,
  // removeMaskOnSubmit: true,
  clearMaskOnLostFocus: false,
});
$("#deposit").inputmask("decimal", {
  radixPoint: ",",
  groupSeparator: ".",
  allowMinus: false,
  suffix: " ₺",
  digits: 2,
  digitsOptional: false,
  rightAlign: true,
  unmaskAsNumber: true,
  // removeMaskOnSubmit: true,
  clearMaskOnLostFocus: false,
});
$("#discount").inputmask("decimal", {
  radixPoint: ",",
  groupSeparator: ".",
  allowMinus: false,
  suffix: " ₺",
  digits: 2,
  digitsOptional: false,
  rightAlign: true,
  unmaskAsNumber: true,
  // removeMaskOnSubmit: true,
  clearMaskOnLostFocus: false,
});
$("#total").inputmask("decimal", {
  radixPoint: ",",
  groupSeparator: ".",
  allowMinus: false,
  suffix: " ₺",
  digits: 2,
  digitsOptional: false,
  rightAlign: true,
  unmaskAsNumber: true,
  // removeMaskOnSubmit: true,
  clearMaskOnLostFocus: false,
});

$("#phone").inputmask("+\\90 (999) 999 99 99");

// selectors
$("#status").select2({
  dropdownAutoWidth: true,
  dropdownCssClass: "increasedzindexclass",
  width: "100%",
});
$("#product_id").on("select2:select", function () {
  let quantity = $("#quantity").val();
  let price =
    $(this).select2("data")[0].price ??
    $("#product_id option").filter(":selected").data("price");
  console.log(price);
  let total = Number(quantity) * Number(price);
  $("#price").val(total.toString().replace(".", ","));
});
$("#quantity").on("input", function () {
  let quantity = $("#quantity");
  if (quantity.val() < 0) {
    quantity.val(1);
    swal("Ürün adedi en az 1 olabilir!", {
      icon: "warning",
      button: "Tamam",
    });
  } else {
    let total =
      Number(
        $("#product_id").select2("data")[0].price ??
          $("#product_id option").filter(":selected").data("price")
      ) * Number(quantity.val());
    $("#price").val(total.toString().replace(".", ","));
  }
});
