<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Family Tree</title>

    <style>
        .connector {
            width: 4px;
            height: 30px;
            background-color: #d1d5db;
            margin: 0 auto;
        }
    </style>
</head>

<body class="bg-gray-100 p-10">

    <div class="max-w-5xl mx-auto">

        <h1 class="text-4xl font-bold text-center text-gray-800 mb-12 tracking-tight">
            Family Tree Viewer
        </h1>

        <!-- GRANDPARENTS -->
        <h2 class="text-xl font-semibold text-gray-700 mb-4 text-center">Grandparents</h2>
        <div class="flex justify-center gap-16 mb-6">
            <x-member-card :member="$pgf" />
            <x-member-card :member="$pgm" />
        </div>

        <div class="connector mb-8"></div>

        <!-- PARENTS -->
        <h2 class="text-xl font-semibold text-gray-700 mb-4 text-center">Parents</h2>
        <div class="flex justify-center gap-16 mb-6">
            <x-member-card :member="$father" />
            <x-member-card :member="$mother" />
        </div>

        <div class="connector mb-8"></div>

        <!-- USER & SPOUSE -->
        <h2 class="text-xl font-semibold text-gray-700 mb-4 text-center">You & Spouse</h2>
        <div class="flex justify-center gap-16 mb-6">
            <x-member-card :member="$user" />
            <x-member-card :member="$spouse" />
        </div>

        <div class="connector mb-8"></div>

        <!-- CHILDREN -->
        <h2 class="text-xl font-semibold text-gray-700 mb-4 text-center">Children</h2>

        <div class="flex justify-center gap-16 mb-16">
            @forelse ($children as $child)
                <x-member-card :member="$child" />
            @empty
                <p class="text-gray-500">No children found.</p>
            @endforelse
        </div>

    </div>

</body>
</html>
