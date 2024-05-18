<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-gray-900 text-white px-6 py-4 flex items-center justify-between">
            <div class="flex items-center">
                <svg class="h-8 w-8 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4091.27 4091.73">
                    <g id="Layer_x0020_1">
                        <path fill="#F7931A" fill-rule="nonzero" d="M4030.06 2540.77c-273.24,1096.01 -1383.32,1763.02 -2479.46,1489.71 -1095.68,-273.24 -1762.69,-1383.39 -1489.33,-2479.31 273.12,-1096.13 1383.2,-1763.19 2479,-1489.95 1096.06,273.24 1763.03,1383.51 1489.76,2479.57l0.02 -0.02z"/>
                        <path fill="white" fill-rule="nonzero" d="M2947.77 1754.38c40.72,-272.26 -166.56,-418.61 -450,-516.24l91.95 -368.8 -224.5 -55.94 -89.51 359.09c-59.02,-14.72 -119.63,-28.59 -179.87,-42.34l90.16 -361.46 -224.36 -55.94 -92 368.68c-48.84,-11.12 -96.81,-22.11 -143.35,-33.69l0.26 -1.16 -309.59 -77.31 -59.72 239.78c0,0 166.56,38.18 163.05,40.53 90.91,22.69 107.35,82.87 104.62,130.57l-104.74 420.15c6.26,1.59 14.38,3.89 23.34,7.49 -7.49,-1.86 -15.46,-3.89 -23.73,-5.87l-146.81 588.57c-11.11,27.62 -39.31,69.07 -102.87,53.33 2.25,3.26 -163.17,-40.72 -163.17,-40.72l-111.46 256.98 292.15 72.83c54.35,13.63 107.61,27.89 160.06,41.3l-92.9 373.03 224.24 55.94 92 -369.07c61.26,16.63 120.71,31.97 178.91,46.43l-91.69 367.33 224.51 55.94 92.89 -372.33c382.82,72.45 670.67,43.24 791.83,-303.02 97.63,-278.78 -4.86,-439.58 -206.26,-544.44 146.69,-33.83 257.18,-130.31 286.64,-329.61l-0.07 -0.05zm-512.93 719.26c-69.38,278.78 -538.76,128.08 -690.94,90.29l123.28 -494.2c152.17,37.99 640.17,113.17 567.67,403.91zm69.43 -723.3c-63.29,253.58 -453.96,124.75 -580.69,93.16l111.77 -448.21c126.73,31.59 534.85,90.55 468.94,355.05l-0.02 0z"/>
                    </g>
                </svg>
                <div>
                    <div class="font-bold text-2xl">BTCUSDT</div>
                    <div class="text-sm text-gray-400">Direction: <span class="font-semibold">Long</span></div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-gray-400 text-sm">Current Mark Price</div>
                <div id="markPrice" class="font-semibold text-xl text-blue-500">12500.00</div>
            </div>
        </div>
        <div class="px-6 py-4">
            <div class="mb-4">
                <div class="text-gray-600 font-medium text-center mb-4 text-xl">Entry Prices</div>
                <div class="flex justify-around text-center text-gray-700">
                    <div class="w-1/3 bg-gray-800 text-white p-4 rounded-lg">
                        <div class="flex flex-col items-center">
                            <x-feathericon-arrow-down class="h-6 w-6 mb-1" />
                            <div class="pb-2 font-semibold text-lg">Lower Price</div>
                            <div class="py-2 text-gray-200">12335.00</div>
                        </div>
                    </div>
                    <div class="w-1/3 bg-gray-800 text-white p-4 mx-2 rounded-lg">
                        <div class="flex flex-col items-center">
                            <x-feathericon-minimize-2 class="h-6 w-6 mb-1" />
                            <div class="pb-2 font-semibold text-lg">OTE</div>
                            <div class="py-2 text-gray-200">12340.00</div>
                        </div>
                    </div>
                    <div class="w-1/3 bg-gray-800 text-white p-4 rounded-lg">
                        <div class="flex flex-col items-center">
                            <x-feathericon-arrow-up class="h-6 w-6 mb-1" />
                            <div class="pb-2 font-semibold text-lg">Higher Price</div>
                            <div class="py-2 text-gray-200">12345.67</div>
                        </div>
                    </div>
                </div>
            </div>

<div class="mb-4">
    <div class="text-gray-600 font-medium text-center mb-4 text-xl">Take Profit Entries</div>
    <div class="space-y-2">
        <div class="flex justify-between bg-gray-800 text-white p-4 rounded-lg items-center">
            <x-feathericon-check class="h-6 w-6 text-green-500" />
            <div class="flex flex-col items-center">
                <span class="font-semibold">Take Profit 1</span>
                <span class="text-gray-200">12400.00</span>
            </div>
            <span class="text-gray-200">2.5% gain</span>
        </div>
        <div class="flex justify-between bg-gray-800 text-white p-4 rounded-lg items-center">
            <x-feathericon-check class="h-6 w-6 text-green-500" />
            <div class="flex flex-col items-center">
                <span class="font-semibold">Take Profit 2</span>
                <span class="text-gray-200">12450.00</span>
            </div>
            <span class="text-gray-200">3.0% gain</span>
        </div>
        <div class="flex justify-between bg-gray-800 text-white p-4 rounded-lg items-center">
            <x-feathericon-check class="h-6 w-6 text-green-500" />
            <div class="flex flex-col items-center">
                <span class="font-semibold">Take Profit 3</span>
                <span class="text-gray-200">12500.00</span>
            </div>
            <span class="text-gray-200">3.5% gain</span>
        </div>
        <div class="flex justify-between bg-gray-800 text-white p-4 rounded-lg items-center">
            <x-feathericon-check class="h-6 w-6 text-green-500" />
            <div class="flex flex-col items-center">
                <span class="font-semibold">Take Profit 4</span>
                <span class="text-gray-200">12550.00</span>
            </div>
            <span class="text-gray-200">4.0% gain</span>
        </div>
        <div class="flex justify-between bg-gray-800 text-white p-4 rounded-lg items-center">
            <x-feathericon-check class="h-6 w-6 text-green-500" />
            <div class="flex flex-col items-center">
                <span class="font-semibold">Take Profit 5</span>
                <span class="text-gray-200">12600.00</span>
            </div>
            <span class="text-gray-200">4.5% gain</span>
        </div>
    </div>
</div>

<div class="mb-4">
    <div class="text-gray-600 font-medium text-center mb-4 text-xl">Stop Loss</div>
    <div class="flex justify-between bg-gray-800 text-white p-4 rounded-lg items-center">
        <x-feathericon-x class="h-6 w-6 text-red-500" />
        <div class="flex flex-col items-center">
            <span class="font-semibold">Stop Loss</span>
            <span class="text-gray-200">12200.00</span>
        </div>
        <span class="text-gray-200">-2.5% loss</span>
    </div>
</div>
<div class="flex justify-between items-center mt-4">
    <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-300">Enter MARKET Price</button>
    <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-300">Enter LIMIT Price</button>
</div>
</body>
</html>
