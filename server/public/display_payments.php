
    <!DOCTYPE html>
    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="🔍Search for names..">

    <?php
    // <?php
try {
    // Retrieve authorized payments
    $authorizedPayments = $stripe->paymentIntents->search([
        'query' => 'status:"requires_capture"',
        'limit' => 100,
    ]);

    // Pagination settings
    $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 5;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $totalItems = count($authorizedPayments->data);
    $totalPages = ceil($totalItems / $limit);
    $offset = ($page - 1) * $limit;
    $authorizedPaymentsSlice = array_slice($authorizedPayments->data, $offset, $limit);

    echo <<<HTML
    <div class="table-container">
    <table class="header-table" style="width: 100%;">
        <tr>
            <th onclick="sortTable(0)">Name</th>
            <th onclick="sortTable(1)">Amount</th>
            <th onclick="sortTable(2)">Reservation Number</th>
            <th>Action</th>
        </tr>
    HTML;

    // Display authorized payments
    foreach ($authorizedPaymentsSlice as $payment) {
        // Display payment details
        echo <<<HTML
        <tr>
            <td>{$payment->metadata->name}</td>
            <td>{$payment->amount}</td>
            <td>{$payment->metadata->reservation_number}</td>
            <td>
                <form action='captured.php' method='POST'>
                    <input type='hidden' name='payment_intent' value='{$payment->id}' />
                    <button type='submit'>Capture</button>
                </form>
            </td>
        </tr>
        HTML;
    }

    echo "</table>";
    echo "</div>";

    // Pagination buttons
    echo "<div style='margin-top: 10px;'>";
    if ($page > 1) {
        echo "<a href='?page=" . ($page - 1) . "&limit=$limit'>Previous</a>";
    }
    if ($page < $totalPages) {
        echo "<a href='?page=" . ($page + 1) . "&limit=$limit'>Next</a>";
    }
    echo "</div>";
} catch (\Throwable $th) {
    echo $th;
}
?>
<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th, td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f0f0f0;
        cursor: pointer;
    }
    th.active {
        background-color: #d4f1d2;
    }
    button {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 8px;
    }
    input[type="number"] {
        width: 60px;
    }
    .table-container {
        overflow-y: auto;
    }
    .header-table {
        position: sticky;
        top: 0;
        z-index: 1;
    }
</style>

<div>
    <form action="" method="GET">
        <label for="limit">Show:</label>
        <input type="number" id="limit" name="limit" min="1" max="100" value="<?= $limit ?>">
        <button type="submit">Apply</button>
    </form>
</div>

<script>
    // Search function
    function searchTable() {
        let input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.querySelector("table");
        tr = table.getElementsByTagName("tr");

        for (i = 1; i < tr.length; i++) {
            let found = false;
            td = tr[i].getElementsByTagName("td");
            for (let j = 0; j < td.length; j++) {
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
            }
            tr[i].style.display = found ? "" : "none";
        }
    }

    // Sorting function
    let currentSortColumn = -1;
  // Sort the table rows based on the selected column
  function sortTable(col) {
        const table = document.querySelector("table");
        const tbody = table.querySelector("tbody");
        const rows = Array.from(tbody.querySelectorAll("tr"));

        const sortedRows = rows.sort((a, b) => {
            const aText = a.cells[col].textContent.trim();
            const bText = b.cells[col].textContent.trim();
            return aText.localeCompare(bText, undefined, { numeric: true, sensitivity: 'base' });
        });

        if (currentSortColumn === col) {
            sortedRows.reverse();
            currentSortColumn = -1;
        } else {
            currentSortColumn = col;
        }

        tbody.innerHTML = '';
        sortedRows.forEach(row => tbody.appendChild(row));

        const ths = table.querySelectorAll("th");
        ths.forEach(th => th.classList.remove("active"));
        ths[col].classList.add("active");
    }

        // Synchronize horizontal scroll positions between header table and main table
        const headerTable = document.querySelector(".header-table");
    const mainTable = document.querySelector(".table-container table:not(.header-table)");

    mainTable.addEventListener("scroll", function () {
        headerTable.scrollLeft = mainTable.scrollLeft;
    });

</script>
