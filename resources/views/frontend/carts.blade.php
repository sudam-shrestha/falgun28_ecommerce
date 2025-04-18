<x-frontend-layout>
    <section>
        <div class="container py-10">
            <h1 class="text-3xl font-bold text-center mb-8">
                Your Cart
            </h1>

            @forelse ($carts as $sellerId => $items)
                @php
                    $totalAmt = 0;
                @endphp
                <div class="border border-gray-300 rounded-lg p-4 mb-6">
                    <h2 class="text-xl font-semibold mb-3">
                        Seller: {{ $items->first()->product->seller->name ?? 'Unknown Seller' }}
                    </h2>

                    <ul class="space-y-4">
                        @foreach ($items as $cart)
                            @php
                                $totalAmt += $cart->amount;
                            @endphp
                            <li class="flex justify-between items-center border-b pb-2">
                                <div>
                                    <p class="font-medium">{{ $cart->product->name }}</p>
                                    <label for="quantity">Qty:</label>
                                    <input type="number" class="quantity-input border rounded px-2 py-1"
                                        data-cart-id="{{ $cart->id }}" min="1" value="{{ $cart->quantity }}">
                                </div>
                                <p class="text-right font-semibold amount-display">Rs.
                                    {{ number_format($cart->amount, 2) }}</p>
                            </li>

                            <div class="flex justify-end gap-2">
                                <form action="{{ route('cart.delete', $cart->id) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <button type="submit"
                                        class="bg-red-600 px-2 py-1 text-white rounded">Remove</button>
                                </form>

                            </div>
                        @endforeach
                        <div>
                            <b>Total:</b>RS.{{ $totalAmt }}
                        </div>



                        <!-- Modal toggle -->
                        <button data-modal-target="payment_method" data-modal-toggle="payment_method"
                            class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                            type="button">
                            Proceed To CheckOut
                        </button>

                        <!-- Main modal -->
                        <div id="payment_method" tabindex="-1" aria-hidden="true"
                            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                            <div class="relative p-4 w-full max-w-2xl max-h-full">
                                <!-- Modal content -->
                                <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                                    <!-- Modal header -->
                                    <div
                                        class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                            Select Payment Method
                                        </h3>
                                        <button type="button"
                                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                            data-modal-hide="payment_method">
                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 14 14">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                            </svg>
                                            <span class="sr-only">Close modal</span>
                                        </button>
                                    </div>
                                    <!-- Modal body -->
                                    <div class="p-4 md:p-5 space-y-4">
                                        <form action="{{ route('order') }}" method="post">
                                            @csrf
                                            <input type="text" name="total_amount" value="{{ $totalAmt }}"
                                                hidden>
                                            <input type="text" name="seller_id"
                                                value="{{ $items->first()->product->seller->id }}" hidden>

                                            <select name="payment_method" id="payment_method">
                                                <option value="khalti">Khalti</option>
                                                <option value="esewa">Esewa</option>
                                            </select>
                                            <button type="submit"
                                                class="bg-secondary px-2 py-1 text-white rounded">Order</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </ul>
                </div>
            @empty
                <p class="text-center text-gray-500">Your cart is empty.</p>
            @endforelse
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', function() {
                    const cartId = this.dataset.cartId;
                    const quantity = this.value;
                    const amountDisplay = this.closest('li').querySelector('.amount-display');

                    fetch("{{ route('cart.updateQuantity') }}", {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                cart_id: cartId,
                                quantity: quantity
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status) {
                                amountDisplay.innerText = `Rs. ${data.amount}`;
                            } else {
                                alert(data.message || 'Failed to update quantity');
                            }
                        });
                });
            });
        });
    </script>

</x-frontend-layout>
