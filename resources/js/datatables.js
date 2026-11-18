import DataTable from "datatables.net";

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".datatable").forEach((tabla) => {
        // Evita inicializar dos veces la misma tabla.
        if (DataTable.isDataTable(tabla)) {
            return;
        }

        const contenedor = tabla.closest(".dt-container");

        new DataTable(tabla, {
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            searching: true,
            ordering: true,
            paging: true,
            info: true,
            autoWidth: false,
            deferRender: true,

            language: {
                emptyTable: "No hay registros disponibles",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                lengthMenu: "Mostrar _MENU_ registros",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                search: "Buscar:",
                zeroRecords: "No se encontraron resultados",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior",
                },
            },

            initComplete: function () {
                this.api().table().node().classList.add("dt-inicializada");
            },
        });
    });
});
