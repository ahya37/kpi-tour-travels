const table = $(".data").DataTable({
  pageLength: 10,

  bLengthChange: true,
  bFilter: true,
  bInfo: true,
  processing: true,
  bServerSide: true,
  order: [[1, "desc"]],
  autoWidth: false,
  ajax: {
    url: "/marketings/target/list",
    type: "POST",
    data: function (q) {
      q._token = CSRF_TOKEN;
      return q;
    },
  },
  columnDefs: [
    {
      targets: 0,
      visible: false,
      render: function (data, type, row, meta) {
        return `<p>${row.id}</p>`;
      },
    },
    {
      targets: 1,
      render: function (data, type, row, meta) {
        return `<p>${row.year}</p>`;
      },
    },
    {
      targets: 2,
      render: function (data, type, row, meta) {
        return `<p>${row.total_target}</p>`;
      },
    },
    {
      targets: 3,
      render: function (data, type, row, meta) {
        return `<p>${row.total_realization}</p>`;
      },
    },
    {
      targets: 4,
      render: function (data, type, row, meta) {
        return `<p>${row.total_difference}</p>`;
      },
    },
    {
      targets: 5,
      render: function (data, type, row, meta) {
        return `
           <a href="/marketings/target/detail/${row.id}" class="btn btn-sm btn-primary text-white" title="Detail"><i class="fa fa-eye"></i></a >
          `;
      },
    },
  ],
});