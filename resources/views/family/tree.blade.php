<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Family Tree</title>

    <style>
        .connector {
            width: 2px;
            height: 40px;
            background-color: #b6b6b6;
            margin: 0 auto;
        }
    </style>

    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="bg-[#edf6f3]">

    <!-- HEADER TITLE -->
    <div class="bg-[#02632c] py-6 shadow-lg mb-10">
        <h1 class="text-center text-white text-4xl font-bold tracking-wide">
            परिवारिक वंशवृक्ष (Family Tree Viewer)
        </h1>
        <p class="text-center text-green-100 text-lg mt-1">
            ऐपन ब्राह्मण समाज - Connecting Tradition, Family & Community
        </p>
    </div>

    <div class="max-w-6xl mx-auto p-8 bg-white shadow-xl rounded-xl border border-gray-200">

        <!-- INSTRUCTIONS -->
        <div class="bg-green-50 border-l-4 border-green-600 p-6 rounded mb-10">
            <h2 class="text-2xl font-semibold text-green-700 mb-3">
                📌 Instructions / निर्देशन
            </h2>

            <ul class="space-y-2 text-gray-700 leading-relaxed">
                <li>✔ Enter a member ID to visualize a 4-level family tree.</li>
                <li>✔ Hover over any member’s photo to see full details.</li>
                <li>✔ Missing members will automatically show a message (e.g., “No father data available”).</li>
                <li>✔ Use this tool to explore family lineage and relationships.</li>
            </ul>
        </div>

        <!-- FAMILY TREE -->
        <!-- GRANDPARENTS -->
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center border-b pb-2">
            Grandparents / हजुरबुबा–हजुरआमा
        </h2>

        <div class="flex justify-center gap-16 mb-6">
            @if($pgf)
                <x-member-card :member="$pgf" />
            @else
                <x-member-card :member="null" label="No grandfather data available" />
            @endif

            @if($pgm)
                <x-member-card :member="$pgm" />
            @else
                <x-member-card :member="null" label="No grandmother data available" />
            @endif
        </div>

        <div class="connector mb-8"></div>

        <!-- PARENTS -->
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center border-b pb-2">
            Parents / बाबु–आमा
        </h2>

        <div class="flex justify-center gap-16 mb-6">
            @if($father)
                <x-member-card :member="$father" />
            @else
                <x-member-card :member="null" label="No father data available" />
            @endif

            @if($mother)
                <x-member-card :member="$mother" />
            @else
                <x-member-card :member="null" label="No mother data available" />
            @endif
        </div>

        <div class="connector mb-8"></div>

        <!-- USER & SPOUSE -->
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center border-b pb-2">
            You & Spouse / तपाईं र जीवनसाथी
        </h2>

        <div class="flex justify-center gap-16 mb-6">
            <x-member-card :member="$user" />

            @if($spouse)
                <x-member-card :member="$spouse" />
            @else
                <x-member-card :member="null" label="No spouse data available" />
            @endif
        </div>

        <div class="connector mb-8"></div>

        <!-- CHILDREN -->
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center border-b pb-2">
            Children / सन्तान
        </h2>

        <div class="flex justify-center gap-16 mb-16">
            @forelse ($children as $child)
                <x-member-card :member="$child" />
            @empty
                <x-member-card :member="null" label="No children found" />
            @endforelse
        </div>

    </div>

    <script>
        feather.replace();
    </script>

</body>
</html>
