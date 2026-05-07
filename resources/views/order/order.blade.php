<p><a href="{{ route('dashboard') }}">Back to Dashboard</a></p>

<h2>Order Management</h2>

<div style="border:1px solid #ccc; padding:15px; margin-bottom:20px;">
    <h3 id="formTitle">Create New Order (POS)</h3>
    <form method="POST" action="{{ route('orders.store') }}" id="orderForm">
        @csrf
        <input type="hidden" name="_method" id="methodField" value="POST">
        <input type="hidden" name="order_id" id="orderId">

        <p>
            <label>Customer Name:</label><br>
            <input type="text" name="customer_name" id="customer_name" required>
        </p>
        <p>
            <label>Shoe Product:</label><br>
            <select name="shoe_product_id" id="menu_id" required>
                <option value="">Select product</option>
                @foreach($shoeProducts as $shoeProduct)
                    <option value="{{ $shoeProduct->id }}" data-price="{{ $shoeProduct->price }}">
                        {{ $shoeProduct->name }} – {{ $shoeProduct->color ?? '' }} {{ $shoeProduct->size ?? '' }} (₱{{ number_format($shoeProduct->price,2) }} – Stock: {{ $shoeProduct->stock }} pairs)
                    </option>
                @endforeach
            </select>
        </p>
        <p>
            <label>Quantity (pairs):</label><br>
            <input type="number" name="quantity" id="quantity" step="1" min="1" required>
        </p>
        <p>
            <strong>Total Cost: </strong> <span id="totalCost">₱0.00</span>
        </p>
        <button type="submit" id="submitBtn">Create Order</button>
        <button type="button" id="cancelBtn" style="display:none;" onclick="resetForm()">Cancel</button>
    </form>
</div>

<h3>Order Summary</h3>
<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width:100%;">
    <thead>
        <tr>
            <th>Order #</th><th>Date</th><th>Customer</th><th>Product</th><th>Qty (pairs)</th>
            <th>Total</th><th>Paid</th><th>Order Status</th><th>Payment Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>#{{ $order->id }}</td>
            <td>{{ $order->created_at->format('Y-m-d') }}</td>
            <td>{{ $order->customer_name }}</td>
            <td>
                {{ $order->shoeProduct->name }}
                @if($order->shoeProduct->size || $order->shoeProduct->color)
                    <br><small>Size: {{ $order->shoeProduct->size ?? '-' }} | Color: {{ $order->shoeProduct->color ?? '-' }}</small>
                @endif
            </td>
            <td>{{ $order->quantity }} pairs</td>
            <td>₱{{ number_format($order->total_price, 2) }}</td>
            <td>₱{{ number_format($order->paid_amount, 2) }}</td>
            <td>{{ ucfirst($order->order_status) }}</td>
            <td>{{ ucfirst($order->payment_status) }}</td>
            <td>
                <button onclick="editOrder({{ $order->id }}, '{{ addslashes($order->customer_name) }}', {{ $order->shoe_product_id }}, {{ $order->quantity }})">Edit</button>
                <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('Delete this order?')">Delete</button>
                </form>
            </td>
        </tr>
        <tr id="details-row-{{ $order->id }}" style="display:none;">
            <td colspan="10" style="background:#f9f9f9; padding:10px;">
                <h4>Order Details</h4>
                <table border="0">
                    <tr><th>Customer:</th><td>{{ $order->customer_name }}</td></tr>
                    <tr><th>Product:</th><td>{{ $order->shoeProduct->name }} ({{ $order->shoeProduct->color ?? '' }} {{ $order->shoeProduct->size ?? '' }})</td></tr>
                    <tr><th>Quantity:</th><td>{{ $order->quantity }} pairs</td></tr>
                    <tr><th>Unit Price:</th><td>₱{{ number_format($order->unit_price,2) }}</td></tr>
                    <tr><th>Total Price:</th><td>₱{{ number_format($order->total_price,2) }}</td></tr>
                    <tr><th>Amount Paid:</th><td>₱{{ number_format($order->paid_amount,2) }}</td></tr>
                    <tr><th>Remaining Balance:</th><td>₱{{ number_format($order->remaining_balance,2) }}</td></tr>
                    <td><th>Order Status:</th><td>{{ ucfirst($order->order_status) }}</td></tr>
                    <tr><th>Payment Status:</th><td>{{ ucfirst($order->payment_status) }}</td></tr>
                    <tr><th>Created By:</th><td>{{ $order->user->name }}</td></tr>
                    <tr><th>Date:</th><td>{{ $order->created_at->format('Y-m-d H:i') }}</td></tr>
                </table>
                <h4>Payment History</h4>
                @if($order->payments->count())
                    <table border="1" cellpadding="5">
                        <thead><tr><th>Date</th><th>Amount (₱)</th><th>Change (₱)</th></tr></thead>
                        <tbody>
                            @foreach($order->payments as $payment)
                            <tr><td>{{ $payment->payment_date->format('Y-m-d H:i') }}</td>
                                <td>₱{{ number_format($payment->amount_paid,2) }}</td>
                                <td>₱{{ number_format($payment->change_given,2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No payments yet.</p>
                @endif
                <p><a href="{{ route('payments.index', ['order_id' => $order->id]) }}">Make a payment</a></p>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $orders->links() }}

<script>
    const menuSelect = document.getElementById('menu_id');
    const qtyInput = document.getElementById('quantity');
    const totalSpan = document.getElementById('totalCost');

    function updateTotal() {
        let price = 0;
        if (menuSelect.selectedIndex > 0) {
            price = parseFloat(menuSelect.options[menuSelect.selectedIndex].dataset.price) || 0;
        }
        let qty = parseFloat(qtyInput.value) || 0;
        totalSpan.innerText = '₱' + (price * qty).toFixed(2);
    }
    menuSelect.addEventListener('change', updateTotal);
    qtyInput.addEventListener('input', updateTotal);

    function resetForm() {
        document.getElementById('orderForm').action = "{{ route('orders.store') }}";
        document.getElementById('methodField').value = 'POST';
        document.getElementById('orderId').value = '';
        document.getElementById('customer_name').value = '';
        document.getElementById('menu_id').value = '';
        document.getElementById('quantity').value = '';
        document.getElementById('formTitle').innerText = 'Create New Order (POS)';
        document.getElementById('submitBtn').innerText = 'Create Order';
        document.getElementById('cancelBtn').style.display = 'none';
        updateTotal();
    }

    function editOrder(id, customerName, shoeProductId, quantity) {
        document.getElementById('orderForm').action = "/orders/" + id;
        document.getElementById('methodField').value = 'PUT';
        document.getElementById('orderId').value = id;
        document.getElementById('customer_name').value = customerName;
        document.getElementById('menu_id').value = shoeProductId;
        document.getElementById('quantity').value = quantity;
        document.getElementById('formTitle').innerText = 'Edit Order #' + id;
        document.getElementById('submitBtn').innerText = 'Update Order';
        document.getElementById('cancelBtn').style.display = 'inline-block';
        updateTotal();
    }

    function toggleDetails(orderId) {
        const row = document.getElementById('details-row-' + orderId);
        row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
    }
</script>