<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<?php
$node = new node(28);
$consumptionLast12Months = array_slice($node->consumptionByMonth(), 0, 12, true);

printArray($consumptionLast12Months);
?>