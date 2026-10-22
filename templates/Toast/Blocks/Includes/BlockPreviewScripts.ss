<script type="module">if (window.self !== window.top) (async function() {<% loop $Paths %>await import('$Path.JS');<% end_loop %>})();</script>
