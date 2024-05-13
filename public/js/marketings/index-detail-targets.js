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
    url: `/marketings/target/detail/list/${detailMarketingTargetId}`,
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
        return row.id;
      },
    },
    {
      targets: 1,
      render: function (data, type, row, meta) {
        return row.month_name;
      },
    },
    {
      targets: 2,
      render: function (data, type, row, meta) {
        return row.program;
      },
    },
    {
      targets: 3,
      render: function (data, type, row, meta) {
        return row.target;
      },
    },
    {
      targets: 4,
      render: function (data, type, row, meta) {
        return row.realization;
      },
    },
    {
      targets: 5,
      render: function (data, type, row, meta) {
        return row.difference;
      },
    },
    {
      targets: 6,
      render: function (data, type, row, meta) {
        return `<a href="/marketings/target/detail/${row.id}" class="btn btn-sm btn-primary text-white" title="Detail"><i class="fa fa-eye"></i></a >`;
      },
    },
  ],
});