<x-app-layout>
<div class="bg-white shadow rounded p-6">
  <h2 class="text-xl font-semibold mb-4">Add Family Member</h2>

  <form id="memberForm" class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium">First name</label>
        <input type="text" name="first_name" id="first_name" class="mt-1 block w-full border rounded px-3 py-2" required>
      </div>
      <div>
        <label class="block text-sm font-medium">Last name</label>
        <input type="text" name="last_name" id="last_name" class="mt-1 block w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium">Gender</label>
        <select name="gender" id="gender" class="mt-1 block w-full border rounded px-3 py-2">
          <option value="">Select</option>
          <option value="male">Male</option>
          <option value="female">Female</option>
          <option value="other">Other</option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium">Father (select existing)</label>
        <input type="text" id="father_search" placeholder="Search father..." class="mt-1 block w-full border rounded px-3 py-2">
        <input type="hidden" id="father_id" name="father_id">
      </div>
      <div>
        <label class="block text-sm font-medium">Mother (select existing)</label>
        <input type="text" id="mother_search" placeholder="Search mother..." class="mt-1 block w-full border rounded px-3 py-2">
        <input type="hidden" id="mother_id" name="mother_id">
      </div>
      <div>
        <label class="block text-sm font-medium">Spouse (select existing)</label>
        <input type="text" id="spouse_search" placeholder="Search spouse..." class="mt-1 block w-full border rounded px-3 py-2">
        <input type="hidden" id="spouse_id" name="spouse_id">
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium">Occupation</label>
        <input type="text" name="occupation" id="occupation" class="mt-1 block w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium">Gotra</label>
        <input type="text" name="gotra" id="gotra" class="mt-1 block w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium">Mul</label>
        <input type="text" name="mul" id="mul" class="mt-1 block w-full border rounded px-3 py-2">
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium">Date of birth</label>
        <input type="date" name="dob" id="dob" class="mt-1 block w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium">Citizenship</label>
        <input type="text" id="citizenship_search" placeholder="Type a country (will create if not exists)" class="mt-1 block w-full border rounded px-3 py-2">
        <input type="hidden" id="citizenship_id" name="citizenship_id">
      </div>
      <div>
        <label class="block text-sm font-medium">Photo (filename in public/uploads optional)</label>
        <input type="text" name="photo" id="photo" placeholder="filename.jpg" class="mt-1 block w-full border rounded px-3 py-2">
      </div>
    </div>

    <div class="flex items-center gap-3">
      <button type="submit" class="bg-teal-600 text-white px-4 py-2 rounded">Save</button>
      <a href="{{ route('members.index') }}" class="text-sm text-gray-600">Back to list</a>
    </div>
  </form>
</div>

<script>
(function(){
  // helper to search existing members (used by father/mother/spouse inputs)
  async function searchMembers(q) {
    if (!q) return [];
    const r = await fetch(`/api/members?search=${encodeURIComponent(q)}`);
    return await r.json();
  }

  async function attachAutocomplete(inputEl, hiddenEl) {
    inputEl.addEventListener('input', async function() {
      const q = this.value.trim();
      if (!q) {
        hiddenEl.value = '';
        return;
      }
      const results = await searchMembers(q);
      // If there is exact match (first+last), auto-select it
      if (results.length === 1) {
        hiddenEl.value = results[0].id;
      } else {
        hiddenEl.value = '';
      }
      // For better UX you could show a dropdown — keep simple: enable click to autofill existing member
      // We'll also allow clicking suggestion by showing confirm dialog
      if (results.length > 0) {
        // show top 3 suggestions inline (simple)
        let html = results.slice(0,5).map(m => `${m.id}: ${m.first_name} ${m.last_name} (${m.occupation||''})`).join('\n');
        console.log('Suggestions:', html);
      }
    });

    // On blur, if value looks like "id: name" parse it
    inputEl.addEventListener('change', async function(){
      const v = this.value.trim();
      // if user typed a numeric id
      if (/^\d+$/.test(v)) {
        const r = await fetch(`/api/members/${v}`);
        if (r.ok) {
          const m = await r.json();
          hiddenEl.value = m.id;
          // auto fill form fields from selected member
          fillFormFromMember(m);
        }
      }
    });
  }

  function fillFormFromMember(m) {
    if (!m) return;
    // fill relevant fields if empty
    document.getElementById('first_name').value = m.first_name || document.getElementById('first_name').value;
    document.getElementById('last_name').value = m.last_name || document.getElementById('last_name').value;
    document.getElementById('gender').value = m.gender || document.getElementById('gender').value;
    document.getElementById('occupation').value = m.occupation || document.getElementById('occupation').value;
    document.getElementById('gotra').value = m.gotra || document.getElementById('gotra').value;
    document.getElementById('mul').value = m.mul || document.getElementById('mul').value;
    if (m.citizenship) {
      document.getElementById('citizenship_search').value = m.citizenship.country || m.citizenship;
      document.getElementById('citizenship_id').value = m.citizenship_id || '';
    }
  }

  // attach autocomplete on father/mother/spouse inputs
  attachAutocomplete(document.getElementById('father_search'), document.getElementById('father_id'));
  attachAutocomplete(document.getElementById('mother_search'), document.getElementById('mother_id'));
  attachAutocomplete(document.getElementById('spouse_search'), document.getElementById('spouse_id'));

  // Citizenship input: if user types existing country we will fetch id on submit, else create server-side
  document.getElementById('memberForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const payload = {
      first_name: document.getElementById('first_name').value.trim(),
      last_name: document.getElementById('last_name').value.trim(),
      gender: document.getElementById('gender').value,
      father_id: document.getElementById('father_id').value || null,
      mother_id: document.getElementById('mother_id').value || null,
      spouse_id: document.getElementById('spouse_id').value || null,
      occupation: document.getElementById('occupation').value.trim(),
      gotra: document.getElementById('gotra').value.trim(),
      mul: document.getElementById('mul').value.trim(),
      dob: document.getElementById('dob').value || null,
      citizenship_id: document.getElementById('citizenship_id').value || null,
      citizenship_country: document.getElementById('citizenship_search').value.trim() || null,
      photo: document.getElementById('photo').value || null
    };

    const res = await fetch('/api/members', {
      method: 'POST',
      headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
      body: JSON.stringify(payload)
    });

    if (res.status === 201) {
      const dd = await res.json();
      alert('Member created: ' + dd.member.first_name);
      // redirect to tree for new member
      window.location.href = `/members/${dd.member.id}/tree`;
    } else {
      const err = await res.json().catch(()=>({}));
      alert('Error saving: ' + (err.message || 'validation error'));
    }
  });

})();
</script>
</x-app-layout>
