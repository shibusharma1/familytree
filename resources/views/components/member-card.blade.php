@props(['member', 'label' => null])

<div class="relative group flex flex-col items-center">

    {{-- PHOTO --}}
    @if($member)
    <img src="{{ $member && $member->photo 
        ? asset('storage/' . $member->photo) 
        : ($member->gender === 'female' 
            ? asset('woman.png') 
            : asset('man.png')) 
    }}" class="rounded-full shadow-lg bg-white w-[90px] h-[90px] object-cover border-4 border-green-600" />


    @endif
    {{-- NAME --}}
    <p class="mt-3 text-gray-800 font-bold">
        @if($member)
        {{ $member->first_name.' '.$member->last_name }}
        @else
        <span class="text-gray-500">{{ $label ?? 'Unknown' }}</span>
        @endif
    </p>

    {{-- HOVER POPUP --}}
    <div
        class="absolute top-32 z-50 hidden group-hover:block bg-white shadow-2xl p-5 rounded-lg w-72 text-left border border-gray-300">

        @if($member)
        <h3 class="font-bold text-lg text-green-700 mb-3">Details</h3>

        <p><b>Name:</b> {{ $member->first_name }} {{ $member->last_name }}</p>
        <p><b>Gender:</b> {{ ucfirst($member->gender) }}</p>
        <p><b>Occupation:</b> {{ $member->occupation ?? 'N/A' }}</p>
        <p><b>Gotra:</b> {{ $member->gotra ?? 'N/A' }}</p>
        <p><b>Mul:</b> {{ $member->mul ?? 'N/A' }}</p>
        <p><b>Date of Birth:</b> {{ $member->dob ?? 'N/A' }}</p>
        <p><b>Date of Death:</b> {{ $member->dod ?? 'N/A' }}</p>

        @else
        <h3 class="font-bold text-lg text-red-600 mb-3">No Data Available</h3>
        <p class="text-gray-600">{{ $label }}</p>
        @endif

    </div>

</div>