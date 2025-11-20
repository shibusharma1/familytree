@props(['member'])

<div class="relative group flex flex-col items-center">

    <img 
        src="{{ asset('male.jpg') }}"
        class="rounded-full shadow-xl bg-white w-[80px] h-[80px] object-cover border-4 border-white"
    />

    <p class="mt-3 text-gray-700 font-semibold">
        {{ $member ? $member->first_name.' '.$member->last_name : 'Unknown' }}
    </p>

    <!-- Hover Info -->
    <div class="absolute top-32 z-50 hidden group-hover:block bg-white shadow-xl p-4 rounded-lg w-64 text-left border border-gray-200">

        @if($member)
        <p><b>Gender:</b> {{ ucfirst($member->gender) }}</p>
        <p><b>Occupation:</b> {{ $member->occupation ?? 'N/A' }}</p>
        <p><b>Gotra:</b> {{ $member->gotra ?? 'N/A' }}</p>
        <p><b>Mul:</b> {{ $member->mul ?? 'N/A' }}</p>
        <p><b>DOB:</b> {{ $member->dob ?? 'N/A' }}</p>
        <p><b>DOD:</b> {{ $member->dod ?? 'N/A' }}</p>
        @else
        <p class="text-gray-500">No data available</p>
        @endif

    </div>

</div>
