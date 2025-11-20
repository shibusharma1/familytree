<x-app-layout>
  <div class="bg-white shadow rounded p-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-xl font-semibold">Family tree for: {{ $member->full_name }}</h2>
        <div class="text-sm text-gray-500">
          Gotra: {{ $member->gotra ?? '-' }} • Mul: {{ $member->mul ?? '-' }} • Citizenship:
          {{ $member->citizenship ? $member->citizenship->country : '-' }}
        </div>
      </div>

      <div class="flex gap-2">
        <button id="fit" class="px-3 py-1 bg-gray-200 rounded">Fit</button>
        <button id="zoomIn" class="px-3 py-1 bg-gray-200 rounded">＋</button>
        <button id="zoomOut" class="px-3 py-1 bg-gray-200 rounded">－</button>
      </div>
    </div>

    <div id="treeWrapper" class="relative"
      style="height:72vh; border-radius:8px; overflow:hidden; background:linear-gradient(180deg,#fff,#f8fafc);">
      <!-- spinner / status -->
      <div id="treeStatus" class="absolute inset-0 flex items-center justify-center bg-white/60 z-20">
        <div class="text-center">
          <svg class="animate-spin h-8 w-8 text-gray-600 mx-auto" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none">
            </circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8h4z"></path>
          </svg>
          <div class="text-sm text-gray-600 mt-2">Loading family tree…</div>
        </div>
      </div>

      <div id="treeContainer" style="height:100%;"></div>
    </div>
  </div>

  <script>
    (function () {
    // center member id (server-side)
    const centerId = {{ (int) $member->id }};
    const apiUrl = `/api/nodes/${centerId}`; // expected to return JSON array of nodes

    // demo image path you uploaded (server will transform this to a proper URL)
    const demoImage = '/mnt/data/4a8ea2aa-2845-43c0-8f00-a03afc735122.png';

    // spinner & container helpers
    const statusEl = document.getElementById('treeStatus');
    const containerEl = document.getElementById('treeContainer');

    function showStatus(text = 'Loading family tree…') {
      statusEl.style.display = 'flex';
      statusEl.querySelector('div .text-sm').textContent = text;
    }
    function hideStatus() { statusEl.style.display = 'none'; }

    // Generic mapping: try to normalize whatever server returns to FamilyTree format:
    // Expected node format for FamilyTree used here: { id, pid (parent id) [optional], name, title, img }
    function mapServerNodes(raw) {
      if (!Array.isArray(raw)) return [];
      // heuristics for common fields
      return raw.map(item => {
        // handle if item is nested or has attributes
        const obj = (typeof item === 'object' && item !== null) ? item : {};

        // id detection
        const id = obj.id ?? obj._id ?? obj.nodeId ?? obj.member_id ?? obj.memberId ?? null;

        // parent detection
        const pid = obj.pid ?? obj.parent_id ?? obj.parentId ?? obj.parent ?? null;

        // name/title detection
        const firstName = obj.first_name ?? obj.firstname ?? obj.given_name ?? obj.name_first ?? '';
        const lastName = obj.last_name ?? obj.lastname ?? obj.family_name ?? obj.name_last ?? '';
        const fullName = (obj.full_name || obj.name || `${firstName} ${lastName}`).trim();

        const title = obj.title ?? obj.position ?? obj.role ?? obj.relation ?? obj.subtitle ?? '';

        // image detection
        let img = obj.img ?? obj.photo ?? obj.avatar ?? obj.picture ?? null;
        // if image is a relative or storage path, leave it. if null, use demo
        if (!img) img = demoImage;

        return {
          id: id ?? Math.random().toString(36).slice(2, 9),
          pid: pid ?? null,
          name: fullName || 'Unknown',
          title: title || '',
          img: img
        };
      });
    }

    // Demo fallback nodes (professional-looking small tree)
    function demoNodes() {
      return [
        { id: '1', name: 'राम प्रसाद शर्मा', title: 'आमा/बुबा', img: demoImage },
        { id: '2', pid: '1', name: 'श्रीकान्त शर्मा', title: 'छोरा', img: demoImage },
        { id: '3', pid: '1', name: 'माया शर्मा', title: 'छोरी', img: demoImage },
        { id: '4', pid: '2', name: 'अर्जुन शर्मा', title: 'पोता', img: demoImage },
        { id: '5', pid: '2', name: 'सुनिता शर्मा', title: 'पोती', img: demoImage }
      ];
    }

    // Utility: safe attempt to create FamilyTree instance
    async function ensureFamilyTreeAvailable() {
      if (window.FamilyTree) return true;

      // Try a list of candidate CDN scripts (some servers may block - we try both)
      const cdnCandidates = [
        'https://unpkg.com/family-tree-js@1.3.0/dist/familytree.min.js',
        'https://cdn.jsdelivr.net/npm/family-tree-js@1.3.0/dist/familytree.min.js'
      ];

      for (const src of cdnCandidates) {
        try {
          await new Promise((resolve, reject) => {
            const s = document.createElement('script');
            s.src = src;
            s.onload = resolve;
            s.onerror = reject;
            s.async = true;
            document.head.appendChild(s);
          });
          if (window.FamilyTree) return true;
        } catch (err) {
          // continue to next candidate
          console.warn('FamilyTree CDN load failed for', src);
        }
      }
      return !!window.FamilyTree;
    }

    // If family tree library unavailable, we'll render fallback HTML tree
    function renderFallbackHtmlTree(nodes) {
      hideStatus();
      containerEl.innerHTML = '';
      // Simple professional-looking tree rendered with nested lists and CSS
      const wrap = document.createElement('div');
      wrap.className = 'p-6 overflow-auto h-full';
      wrap.innerHTML = `
        <div class="text-sm text-gray-600 mb-4">Displaying demo family tree (no data available).</div>
        <div class="bg-white rounded shadow p-4">
          <ul class="space-y-3">
            ${nodes.map(n => `<li class="flex items-center gap-3">
              <img src="${n.img}" alt="${escapeHtml(n.name)}" class="w-12 h-12 rounded object-cover border">
              <div>
                <div class="font-semibold">${escapeHtml(n.name)}</div>
                <div class="text-xs text-gray-500">${escapeHtml(n.title || '')}</div>
              </div>
            </li>`).join('')}
          </ul>
        </div>
      `;
      containerEl.appendChild(wrap);
    }

    // small utility
    function escapeHtml(s) { return String(s || '').replace(/[&<>"']/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"})[m]; }); }

    // main bootstrap
    (async function init() {
      showStatus('Loading family tree data…');

      // fetch nodes
      let rawNodes = null;
      try {
        const res = await fetch(apiUrl, { headers: { 'Accept': 'application/json' }});
        if (!res.ok) throw new Error('Server returned ' + res.status);
        rawNodes = await res.json();
      } catch (err) {
        console.warn('Failed fetching nodes:', err);
        // show demo after a short delay
        showStatus('Could not load tree data. Showing demo...');
        await new Promise(r => setTimeout(r, 700));
        const demo = demoNodes();
        renderFallbackHtmlTree(demo);
        return;
      }

      // normalize nodes
      const nodes = mapServerNodes(rawNodes).filter(n => n && n.id);

      if (!nodes.length) {
        showStatus('No family data found. Showing demo...');
        await new Promise(r => setTimeout(r, 700));
        const demo = demoNodes();
        renderFallbackHtmlTree(demo);
        return;
      }

      // ensure library present
      showStatus('Initializing tree display…');
      const libOk = await ensureFamilyTreeAvailable();

      if (!libOk) {
        console.warn('FamilyTree library not available; falling back to HTML demo rendering.');
        showStatus('Visualization library missing. Showing demo list.');
        await new Promise(r => setTimeout(r, 700));
        renderFallbackHtmlTree(nodes.slice(0, 10));
        return;
      }

      // FamilyTree is available. Create instance and load nodes.
      try {
        // FamilyTree expects nodes as array; but some libraries want pids array for parents.
        // We'll pass 'pid' property (single parent) — adapt if your variant uses 'pids' or 'parent' instead.
        containerEl.innerHTML = ''; // clear spinner overlay remains hidden later
        hideStatus();

        // library-specific options (adjust if you use a particular FamilyTree build)
        const chart = new FamilyTree(containerEl, {
          template: 'tommy',
          enableSearch: false,
          nodeBinding: {
            field_0: 'name',
            field_1: 'title',
            img_0: 'img'
          },
          collapse: { level: 3 },
          zoom: true,
          pan: true,
        });

        // Some FamilyTree variants require different node fields; ensure we provide pid or parent
        // If your API uses parentId or parent, you already mapped to pid above.

        chart.load(nodes);

        // wire the buttons (safe guards)
        const fitBtn = document.getElementById('fit');
        const zoomInBtn = document.getElementById('zoomIn');
        const zoomOutBtn = document.getElementById('zoomOut');

        fitBtn && fitBtn.addEventListener('click', () => {
          try { chart.fit(); } catch (e) { console.warn(e); }
        });
        zoomInBtn && zoomInBtn.addEventListener('click', () => {
          try { chart.zoom(1.2); } catch (e) { console.warn(e); }
        });
        zoomOutBtn && zoomOutBtn.addEventListener('click', () => {
          try { chart.zoom(0.8); } catch (e) { console.warn(e); }
        });

        // node click
        try {
          chart.on && chart.on('click', function(sender, args) {
            const id = args.node && args.node.id;
            if (id) window.open('/members/' + encodeURIComponent(id) + '/tree', '_self');
          });
        } catch (e) {
          // older/newer library variants may use different event APIs
          containerEl.addEventListener('click', function () {});
        }

      } catch (err) {
        console.error('Rendering FamilyTree failed:', err);
        showStatus('Visualization failed — showing demo.');
        await new Promise(r => setTimeout(r, 700));
        renderFallbackHtmlTree(nodes.slice(0, 10));
      }
    })();
  })();
  </script>



  <style>
    /* small styles for fallback list to look good in page */
    #treeContainer img {
      border-radius: 6px;
    }

    #treeContainer .border {
      border: 1px solid #e5e7eb;
    }
  </style>
</x-app-layout>