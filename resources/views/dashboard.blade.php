<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ִ ࣪𖤐 Shoe Store Management System ִ ࣪𖤐') }}
        </h2>
    </x-slot>

    <!--contents here are directly from the guide paper given :> -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!--menu button-->
                    <a href="{{ route('menus.index') }}" style="text-decoration: none; color: inherit;">
                        <h3 class="text-lg font-bold" style="cursor: pointer;">Menu Management</h3>
                    </a>
                    <p>In here you can: ∘ ∘ ∘ ( °ヮ° ) ?</p>
                    <ul style="list-style: disc; margin-left: 20px;">
                        <li>View all available shoes. ˙⋆✮</li>
                        <li>Add new shoe products. ˙⋆✮</li>
                        <li>View and manage existing shoe products. ˙⋆✮</li>
                        <li>Update shoe details (name, category, price, stock). ˙⋆✮</li>
                        <li>Delete shoe products when no longer available. ˙⋆✮</li>
                    </ul>
                    <hr class="my-4">

                    <!--order button-->
                    <a href="{{ route('orders.index') }}" style="text-decoration: none; color: inherit;">
                        <h3 class="text-lg font-bold" style="cursor: pointer;">Order Management</h3>
                    </a>
                    <p>In here you can: ∘ ∘ ∘ ( °ヮ° ) ?</p>
                    <ul style="list-style: disc; margin-left: 20px;">
                        <li>Use a POS interface for creating orders. ˙⋆✮</li>
                        <li>Create a new order linked to customer, shoe item, and quantity. ˙⋆✮</li>
                        <li>Automatically calculate total cost: <strong>Total = Quantity × Product Price. ˙⋆✮</strong>.</li>
                        <li>Display selected products, quantities, and the overall order total. ˙⋆✮</li>
                        <li>Monitor order status such as Pending, Shipped, or Delivered. ˙⋆✮</li>
                        <li>Display order summary list with with key details such as customer, product, quantity, date, and total cost. ˙⋆✮</li>
                    </ul>
                    <hr class="my-4">

                    <!--payment button-->
                    <a href="{{ route('payments.index') }}" style="text-decoration: none; color: inherit;">
                        <h3 class="text-lg font-bold" style="cursor: pointer;">Payment Management</h3>
                    </a>
                    <p>In here you can: ∘ ∘ ∘ ( °ヮ° ) ?</p>
                    <ul style="list-style: disc; margin-left: 20px;">
                        <li>Process payments for customer orders. ˙⋆✮</li>
                        <li>Update payment status such as Partial, Paid, Unpaid. ˙⋆✮</li>
                        <li>Calculate and display the change based on the amount given. ˙⋆✮</li>
                        <li>View a history of all transactions and payment logs for a specific customer or order. ˙⋆✮</li>
                    </ul>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>