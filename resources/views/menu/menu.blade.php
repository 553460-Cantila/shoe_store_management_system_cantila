<p><a href="{{ route('dashboard') }}">Back to Dashboard</a></p>

<h2>Shoe Products</h2>

<div style="border:1px solid #ccc; padding:15px; margin-bottom:20px;">
    <h3 id="formTitle">Add New Shoe Product</h3>
    <form method="POST" action="{{ route('menus.store') }}" id="productForm">
        @csrf
        <input type="hidden" name="_method" id="methodField" value="POST">
        <input type="hidden" name="id" id="productId">

        <p>
            <label>Name:</label><br>
            <input type="text" name="name" id="name" required>
        </p>
        <p>
            <label>Category:</label><br>
            <input type="text" name="category" id="category" required>
        </p>
        <p>
            <label>Size:</label><br>
            <input type="text" name="size" id="size">
        </p>
        <p>
            <label>Color:</label><br>
            <input type="text" name="color" id="color">
        </p>
        <p>
            <label>Price per pair (₱):</label><br>
            <input type="number" step="0.01" name="price" id="price" required>
        </p>
        <p>
            <label>Stock (pairs):</label><br>
            <input type="number" name="stock" id="stock" required>
        </p>
        <button type="submit" id="submitBtn">Save Product</button>
        <button type="button" id="cancelBtn" style="display:none;" onclick="resetForm()">Cancel</button>
    </form>
</div>

<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width:100%;">
    <thead>
        <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Size</th>
            <th>Color</th>
            <th>Price (₱)</th>
            <th>Stock (pairs)</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($menus as $menu)
        <tr>
            <td>{{ $menu->name }}</td>
            <td>{{ $menu->category }}</td>
            <td>{{ $menu->size ?? '-' }}</td>
            <td>{{ $menu->color ?? '-' }}</td>
            <td>₱{{ number_format($menu->price, 2) }}</td>
            <td>{{ $menu->stock }}</td>
            <td>
                <button onclick="editProduct({{ $menu->id }}, '{{ addslashes($menu->name) }}',
                '{{ addslashes($menu->category) }}', '{{ addslashes($menu->size) }}',
                '{{ addslashes($menu->color) }}', {{ $menu->price }}, {{ $menu->stock }})">Edit</button>
                <form action="{{ route('menus.destroy', $menu) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('Delete this product?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</tr>
{{ $menus->links() }}

<script>
    function resetForm() {
        document.getElementById('productForm').action = "{{ route('menus.store') }}";
        document.getElementById('methodField').value = 'POST';
        document.getElementById('productId').value = '';
        document.getElementById('name').value = '';
        document.getElementById('category').value = '';
        document.getElementById('size').value = '';
        document.getElementById('color').value = '';
        document.getElementById('price').value = '';
        document.getElementById('stock').value = '';
        document.getElementById('formTitle').innerText = 'Add New Shoe Product';
        document.getElementById('submitBtn').innerText = 'Save Product';
        document.getElementById('cancelBtn').style.display = 'none';
    }

    function editProduct(id, name, category, size, color, price, stock) {
        document.getElementById('productForm').action = "/menus/" + id;
        document.getElementById('methodField').value = 'PUT';
        document.getElementById('productId').value = id;
        document.getElementById('name').value = name;
        document.getElementById('category').value = category;
        document.getElementById('size').value = size;
        document.getElementById('color').value = color;
        document.getElementById('price').value = price;
        document.getElementById('stock').value = stock;
        document.getElementById('formTitle').innerText = 'Edit Shoe Product';
        document.getElementById('submitBtn').innerText = 'Update Product';
        document.getElementById('cancelBtn').style.display = 'inline-block';
    }
</script>