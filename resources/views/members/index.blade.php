<x-app-layout>
<div class="bg-white shadow rounded p-6">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-semibold">Family Members</h2>
    <a href="{{ route('members.create') }}" class="bg-teal-600 text-white px-4 py-2 rounded">Add member</a>
  </div>

  <div class="mb-4">
    <input id="searchInput" type="text" placeholder="Search by name, occupation, gotra, mul or citizenship" class="w-full border rounded px-3 py-2">
  </div>

  <div id="results" class="space-y-2"></div>
</div>

<script>
(async function(){
  const input = document.getElementById('searchInput');
  const results = document.getElementById('results');

  async function doSearch(q){
    const r = await fetch('/api/members?search=' + encodeURIComponent(q));
    const data = await r.json();
    results.innerHTML = '';
    if (data.length === 0) {
      results.innerHTML = '<div class="text-gray-500">No results</div>';
      return;
    }
    data.forEach(m => {
      const div = document.createElement('div');
      div.className = 'p-3 border rounded flex items-center justify-between';
      div.innerHTML = `<div>
        <div class="font-semibold">${m.first_name} ${m.last_name || ''}</div>
        <div class="text-sm text-gray-500">${m.occupation || ''} ${m.gotra ? '• Gotra: '+m.gotra : ''} ${m.mul ? '• Mul: '+m.mul : ''}</div>
      </div>
      <div>
        <a href="/members/${m.id}/tree" class="bg-indigo-600 text-white px-3 py-1 rounded">View tree</a>
      </div>`;
      results.appendChild(div);
    });
  }

  let timeout = null;
  input.addEventListener('input', function(){
    clearTimeout(timeout);
    timeout = setTimeout(()=> doSearch(this.value.trim()), 250);
  });

  // initial load
  doSearch('');
})();
</script>
</x-app-layout>
