<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Product Form</h1>
    <form id="product-form" class="mb-5">
        <div class="mb-3">
            <label for="productName" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="productName" name="product_name" required autocomplete="off">
        </div>
        <div class="mb-3">
            <label for="quantityInStock" class="form-label">Quantity in Stock</label>
            <input type="number" class="form-control" id="quantityInStock" name="quantity" required>
        </div>
        <div class="mb-3">
            <label for="pricePerItem" class="form-label">Price Per Item</label>
            <input type="number" step="0.01" class="form-control" id="pricePerItem" name="price" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <div id="data-table" class="table-responsive"></div>
</div>
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-form">
                    <input type="hidden" id="editIndex">
                    <div class="mb-3">
                        <label for="editProductName" class="form-label">Product Name</label>
                        <input type="text" id="editProductName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editQuantityInStock" class="form-label">Quantity in Stock</label>
                        <input type="number" id="editQuantityInStock" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPricePerItem" class="form-label">Price per Item</label>
                        <input type="number" id="editPricePerItem" class="form-control" step="0.01" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script !src="">
    $(document).ready(function () {
        loadProducts();

        $('#product-form').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: '/save-product',
                method: 'POST',
                data: {
                    product_name: $('#productName').val(),
                    quantity: $('#quantityInStock').val(),
                    price: $('#pricePerItem').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function () {
                    toastr.success('Product added successfully!', 'Success');
                    $('#product-form')[0].reset();
                    loadProducts();
                },
                error: function (xhr) {
                    toastr.error('Failed to add product. Please try again.', 'Error');
                }
            });
        });
        $(document).on('click', '.edit-btn', function () {
            const index = $(this).data('index');
            $('#editIndex').val(index);
            $('#editProductName').val($(this).data('name'));
            $('#editQuantityInStock').val($(this).data('quantity'));
            $('#editPricePerItem').val($(this).data('price'));
            $('#editModal').modal('show');
        });
        $('#edit-form').on('submit', function (e) {
            e.preventDefault();

            const index = $('#editIndex').val();

            $.ajax({
                url: `/update-product/${index}`,
                method: 'PUT',
                data: {
                    product_name: $('#editProductName').val(),
                    quantity: $('#editQuantityInStock').val(),
                    price: $('#editPricePerItem').val(),
                    _token:'{{ csrf_token() }}'
                },
                success: function () {
                    toastr.success('Product updated successfully!', 'Success');
                    $('#editModal').modal('hide');
                    loadProducts();
                },
                error: function (xhr) {
                    toastr.error('Failed to update product. Please try again.', 'Error');
                }
            });
        });

        function loadProducts() {
            $.ajax({
                url: '/get-products',
                method: 'GET',
                success: function (data) {
                    let tableHtml = '<table class="table table-bordered"><thead><tr>' +
                        '<th>Product Name</th><th>Quantity in Stock</th><th>Price Per Item</th>' +
                        '<th>Date Submitted</th><th>Total Value</th><th>Actions</th></tr></thead><tbody>';

                    let totalValueSum = 0;

                    data.forEach((item, index) => {
                        tableHtml += `<tr>
                            <td>${item.product_name}</td>
                            <td>${item.quantity}</td>
                            <td>${item.price}</td>
                            <td>${item.datetime_submitted}</td>
                            <td>${item.total_value}</td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-btn"
                                    data-index="${index}"
                                    data-name="${item.product_name}"
                                    data-quantity="${item.quantity}"
                                    data-price="${item.price}">
                                    Edit
                                </button>
                            </td>
                        </tr>`;
                        totalValueSum += parseFloat(item.total_value);
                    });

                    tableHtml += `<tr>
                        <td colspan="4"><strong>Total</strong></td>
                        <td><strong>${totalValueSum.toFixed(2)}</strong></td>
                        <td></td>
                    </tr></tbody></table>`;

                    $('#data-table').html(tableHtml);
                }
            });
        }
    });
</script>
</body>
</html>
