<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FamilyTree App</title>

  <!-- Tailwind CDN for quick prototyping (replace with proper build in prod) -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- FamilyTreeJS -->
  <script src="https://balkan.app/js/familytree.js"></script>
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/family-tree-js@1.3.0/dist/familytree.min.css">
  <script src="https://cdn.jsdelivr.net/npm/family-tree-js@1.3.0/dist/familytree.min.js" defer></script>


  <style>
    /* small tweaks for FamilyTree node styling if we add custom templates later */
  </style>
</head>

<body class="bg-gray-50 text-gray-800">
  <div class="min-h-screen">
    <div class="max-w-7xl mx-auto p-6">
      {{ $slot ?? $content }}
    </div>
  </div>
</body>

</html>