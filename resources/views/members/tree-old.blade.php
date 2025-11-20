<x-app-layout>
<div class="bg-white shadow rounded p-6">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h2 class="text-xl font-semibold">Family tree for: {{ $member->full_name }}</h2>
      <div class="text-sm text-gray-500">Gotra: {{ $member->gotra ?? '-' }} • Mul: {{ $member->mul ?? '-' }} • Citizenship: {{ $member->citizenship ? $member->citizenship->country : '-' }}</div>
    </div>
    <div class="flex gap-2">
      <button id="fit" class="px-3 py-1 bg-gray-200 rounded">Fit</button>
      <button id="zoomIn" class="px-3 py-1 bg-gray-200 rounded">＋</button>
      <button id="zoomOut" class="px-3 py-1 bg-gray-200 rounded">－</button>
    </div>
  </div>

  <div id="treeContainer" style="height:72vh; border-radius:8px; overflow:hidden; background:linear-gradient(180deg,#fff,#f8fafc);"></div>
</div>

<script>
(async function(){
  const centerId = {{ (int) $member->id }};
  // Fetch nodes for 4-gen tree centered at centerId
  const r = await fetch(`/api/nodes/${centerId}`);
  if (!r.ok) {
    document.getElementById('treeContainer').innerHTML = '<div class="p-4">Could not load tree data.</div>';
    return;
  }
  const nodes = await r.json();

  // Ensure FamilyTree loaded
  if (typeof FamilyTree === 'undefined') {
    document.getElementById('treeContainer').innerHTML = '<div class="p-4">FamilyTreeJS not loaded.</div>';
    return;
  }

  // create the chart
  const chart = new FamilyTree(document.getElementById('treeContainer'), {
    template: 'ana', // choose a built-in template: 'tommy','vinet','ana' etc. change as you like
    enableSearch: false,
    nodeBinding: {
      field_0: "name",
      field_1: "title",
      img_0: "img"
    },
    collapse: {
      level: 3
    },
    zoom: true,
    pan: true,
    // optionally adjust node menu, etc.
  });

  chart.load(nodes);

  document.getElementById('fit').addEventListener('click', ()=> chart.fit());
  document.getElementById('zoomIn').addEventListener('click', ()=> chart.zoom(1.2));
  document.getElementById('zoomOut').addEventListener('click', ()=> chart.zoom(0.8));

  // Optionally: attach node click to show details
  chart.on('click', function(sender, args){
    // args.node contains clicked node data
    const id = args.node.id;
    window.open('/members/' + id + '/tree','_self');
  });

})();
</script>
</x-app-layout>
