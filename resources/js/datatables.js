import DataTable from 'datatables.net';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('table.datatable').forEach((table) => {
        if (DataTable.isDataTable(table)) {
            return;
        }

        const headerCells = Array.from(table.tHead?.rows[0]?.cells ?? []);
        const tbody = table.tBodies[0];

        if (!headerCells.length || !tbody) {
            return;
        }

        const rows = Array.from(tbody.rows);
        const emptyRow = rows.length === 1 && Array.from(rows[0].cells).some((cell) => cell.colSpan > 1);

        if (emptyRow) {
            tbody.replaceChildren();
        }

        const columns = headerCells.map((header) => ({
            orderable: header.dataset.orderable !== 'false',
            searchable: header.dataset.searchable !== 'false',
        }));
        const paging = table.dataset.datatablePaging !== 'false';

        const dataTable = new DataTable(table, {
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            searching: true,
            ordering: true,
            paging,
            lengthChange: paging,
            info: true,
            autoWidth: true,
            deferRender: true,
            scrollX: false,
            order: [],
            columns,
            language: {
                emptyTable: 'No hay registros disponibles',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                infoEmpty: 'Mostrando 0 a 0 de 0 registros',
                infoFiltered: '(filtrado de _MAX_ registros totales)',
                lengthMenu: 'Mostrar _MENU_ registros',
                loadingRecords: 'Cargando...',
                processing: 'Procesando...',
                search: 'Buscar:',
                zeroRecords: 'No se encontraron resultados',
                paginate: {
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior',
                },
            },
        });

        if (table.dataset.columnFilters === 'false') {
            return;
        }

        const searchableColumns = columns
            .map((column, index) => ({ ...column, index }))
            .filter((column) => column.searchable);

        if (!searchableColumns.length) {
            return;
        }

        const wrapper = table.closest('.dt-container');
        const filterPanel = document.createElement('div');
        filterPanel.className = 'dt-column-filters';

        const title = document.createElement('p');
        title.className = 'dt-column-filters-title';
        title.textContent = 'Filtros por columna';
        filterPanel.appendChild(title);

        searchableColumns.forEach(({ index }) => {
            const label = document.createElement('label');
            const labelText = document.createElement('span');
            const input = document.createElement('input');
            const heading = headerCells[index].textContent.trim();

            labelText.textContent = heading;
            input.type = 'search';
            input.placeholder = 'Filtrar ' + heading.toLocaleLowerCase('es');
            input.setAttribute('aria-label', 'Filtrar por ' + heading);
            input.autocomplete = 'off';
            input.addEventListener('input', () => {
                dataTable.column(index).search(input.value).draw();
            });

            label.append(labelText, input);
            filterPanel.appendChild(label);
        });

        const clearButton = document.createElement('button');
        clearButton.type = 'button';
        clearButton.className = 'dt-clear-filters';
        clearButton.textContent = 'Limpiar filtros';
        clearButton.addEventListener('click', () => {
            filterPanel.querySelectorAll('input').forEach((input) => {
                input.value = '';
            });

            searchableColumns.forEach(({ index }) => {
                dataTable.column(index).search('');
            });

            dataTable.draw();
        });
        filterPanel.appendChild(clearButton);

        const tableRegion = wrapper?.querySelector('.dt-layout-table, .dt-scroll');

        if (wrapper && tableRegion) {
            wrapper.insertBefore(filterPanel, tableRegion);
        } else {
            wrapper?.appendChild(filterPanel);
        }
    });
});
