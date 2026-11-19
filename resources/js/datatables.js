import DataTable from 'datatables.net';

const normalizarEncabezado = (valor) =>
    valor
        .toLocaleLowerCase('es')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .trim();

const agregarFiltroFechas = (dataTable, headerRow, contenedor) => {
    const columnaFecha = Array.from(headerRow.cells).findIndex((header) => header.dataset.dateFilter === 'true');

    if (columnaFecha < 0) {
        return;
    }

    const controles = contenedor.querySelector('.dt-layout-row:first-child .dt-layout-start');

    if (!controles) {
        return;
    }

    const filtro = document.createElement('div');
    filtro.className = 'dt-date-filter';
    filtro.setAttribute('role', 'group');
    filtro.setAttribute('aria-label', 'Filtrar registros por fecha');
    filtro.innerHTML =
        '<label>Desde <input type="date" data-date-from aria-label="Fecha inicial"></label>' +
        '<label>Hasta <input type="date" data-date-to aria-label="Fecha final"></label>' +
        '<button type="button" data-date-clear>Limpiar</button>';

    const desde = filtro.querySelector('[data-date-from]');
    const hasta = filtro.querySelector('[data-date-to]');
    const limpiar = filtro.querySelector('[data-date-clear]');

    const aplicarFiltro = () => {
        const fechaInicial = desde.value;
        const fechaFinal = hasta.value;

        if (fechaInicial && fechaFinal && fechaFinal < fechaInicial) {
            hasta.setCustomValidity('La fecha final debe ser igual o posterior a la fecha inicial.');
            hasta.reportValidity();
            return;
        }

        hasta.setCustomValidity('');

        dataTable.search
            .fixed('dateRange', (_texto, _datos, indiceFila) => {
                if (!fechaInicial && !fechaFinal) {
                    return true;
                }

                const fila = dataTable.row(indiceFila).node();
                const fecha = fila?.cells[columnaFecha]?.dataset.order ?? '';

                if (!fecha) {
                    return false;
                }

                return (!fechaInicial || fecha >= fechaInicial) && (!fechaFinal || fecha <= fechaFinal);
            })
            .draw();
    };

    desde.addEventListener('change', aplicarFiltro);
    hasta.addEventListener('change', aplicarFiltro);
    limpiar.addEventListener('click', () => {
        desde.value = '';
        hasta.value = '';
        aplicarFiltro();
    });

    controles.append(filtro);
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('table.datatable').forEach((table) => {
        if (DataTable.isDataTable(table)) {
            return;
        }

        const headerRow = table.tHead?.rows[0];
        const tbody = table.tBodies[0];

        if (!headerRow || !tbody || !headerRow.cells.length) {
            return;
        }

        // Los listados vacíos se representan con el mensaje propio de DataTables.
        const bodyRows = Array.from(tbody.rows);
        const emptyPlaceholder =
            bodyRows.length === 1 && Array.from(bodyRows[0].cells).some((cell) => cell.colSpan > 1);

        if (emptyPlaceholder) {
            tbody.replaceChildren();
        }

        // Las columnas de acciones e imágenes no participan en búsqueda u ordenamiento.
        const columns = Array.from(headerRow.cells).map((header) => {
            const label = normalizarEncabezado(header.textContent || '');
            const isActionColumn = /^(acciones?|opciones?|imagen)$/.test(label);

            return {
                searchable: header.dataset.searchable !== 'false' && !isActionColumn,
                orderable: header.dataset.orderable !== 'false' && !isActionColumn,
            };
        });

        const paging = table.dataset.datatablePaging !== 'false';

        const dataTable = new DataTable(table, {
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            searching: true,
            ordering: true,
            paging,
            lengthChange: paging,
            info: true,
            autoWidth: false,
            deferRender: true,
            scrollX: true,
            scrollCollapse: true,
            order: [],
            columns,
            layout: {
                topStart: paging ? 'pageLength' : null,
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: paging ? 'paging' : null,
            },
            language: {
                emptyTable: 'No hay registros disponibles',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                infoEmpty: 'Mostrando 0 a 0 de 0 registros',
                infoFiltered: '(filtrado de _MAX_ registros totales)',
                lengthMenu: 'Mostrar _MENU_ registros',
                loadingRecords: 'Cargando...',
                processing: 'Procesando...',
                search: 'Buscar:',
                searchPlaceholder: 'Buscar en esta tabla...',
                zeroRecords: 'No se encontraron resultados',
                paginate: {
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior',
                },
            },
        });

        agregarFiltroFechas(dataTable, headerRow, table.closest('.dt-container'));
    });
});
