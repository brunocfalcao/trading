<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trading</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold mb-6">Trading Pairs</h1>
        <form action="{{ route('update-file') }}" method="POST" class="mb-6">
            @csrf
            <label for="fileContent" class="block text-gray-700 font-medium mb-2">File Content:</label>
            <textarea id="fileContent" name="fileContent" rows="10" class="w-full p-4 border border-gray-300 rounded-lg mb-4">{{ $fileContent }}</textarea>
            <div class="flex space-x-4">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Update File</button>
                <form action="{{ route('refresh-file') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">Refresh File</button>
                </form>
            </div>
        </form>

        <h2 class="text-xl font-bold mb-4">Prices</h2>
        @if($signal)
            <div class="grid grid-cols-3 gap-4 mb-6" id="prices">
                <div class="bg-gray-100 p-4 rounded-lg shadow-inner">
                    <div class="text-gray-600">Last Price:</div>
                    <div id="last_price" class="text-2xl font-bold" data-prev="{{ $signal->last_price }}">{{ $signal->last_price }}</div>
                </div>
                <div class="bg-gray-100 p-4 rounded-lg shadow-inner">
                    <div class="text-gray-600">Previous Price:</div>
                    <div id="previous_price" class="text-2xl font-bold" data-prev="{{ $signal->previous_price }}">{{ $signal->previous_price }}</div>
                </div>
                <div class="bg-gray-100 p-4 rounded-lg shadow-inner">
                    <div class="text-gray-600">Older Price:</div>
                    <div id="older_price" class="text-2xl font-bold" data-prev="{{ $signal->older_price }}">{{ $signal->older_price }}</div>
                </div>
            </div>
        @else
            <div class="text-red-500 mb-6">No signal data found for BTCUSDT</div>
        @endif

        <h2 class="text-xl font-bold mb-4">Run Command</h2>
        <form action="{{ route('run-command') }}" method="POST" class="mb-6">
            @csrf
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Run Command</button>
        </form>
        @if(session('commandOutput'))
            <div class="bg-gray-100 p-4 rounded-lg shadow-inner">
                <h3 class="text-lg font-bold mb-2">Command Output</h3>
                <pre class="bg-white p-4 rounded-lg shadow-inner">{{ session('commandOutput') }}</pre>
            </div>
        @endif
    </div>

    <script>
        $(document).ready(function() {
            function fetchPrices() {
                $.ajax({
                    url: "{{ route('refresh-prices') }}",
                    method: 'GET',
                    success: function(data) {
                        updatePrice('last_price', data.last_price);
                        updatePrice('previous_price', data.previous_price);
                        updatePrice('older_price', data.older_price);
                    }
                });
            }

            function updatePrice(id, newValue) {
                var element = $('#' + id);
                var prevValue = parseFloat(element.attr('data-prev'));

                if (newValue > prevValue) {
                    element.removeClass('text-red-500').addClass('text-green-500');
                } else if (newValue < prevValue) {
                    element.removeClass('text-green-500').addClass('text-red-500');
                } else {
                    element.removeClass('text-green-500 text-red-500');
                }

                element.text(newValue);
                element.attr('data-prev', newValue);
            }

            setInterval(fetchPrices, 1000); // Refresh every second
        });
    </script>
</body>
</html>
