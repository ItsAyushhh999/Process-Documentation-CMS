


$('.cTable thead tr')
.clone(true)
.addClass('filters')
.appendTo('.cTable thead');


let table = new DataTable('.cTable', {
  dom: "<'flex justify-between px-3 items-center'<'col-span-1'l><'flex gap-3 items-center'fB>><'overflow-x-auto't><'flex justify-end px-3'p>",
  "lengthMenu": [ [ 50, 100, 250, -1], [ 50, 100, 250,"All"] ],
  "aaSorting": [],
  buttons: [
      {
        extend: 'csv',
        text: 'CSV',
        // exportOptions: {
        //     modifier: {
        //         page: 'current'
        //     }
        // }
    }
  ],
  pagingType: 'full_numbers',
  orderCellsTop: true,
  "aaSorting": [],
  // fixedHeader: true,
  initComplete: function () {
      var api = this.api();
      // For each column
      api
          .columns()
          .eq(0)
          .each(function (colIdx) {
            // console.log("col id", colIdx)
              // Set the header cell to contain the input element
              var cell = $('.filters th').eq(
                  $(api.column(colIdx).header()).index()
              );
              var title = $(cell).text();

              if(title != "Action" && title != "Documents" && title != ""){

              if(title != "Status" && title != "Priority"){
                $(cell).html('<input class="border border-gray-300 dark:border-gray-600 dark:bg-stone-800 rounded text-sm font-normal placeholder:font-normal w-full" type="text" placeholder="' + title + ' Search" />');
                // On every keypress in this input
                $(
                    'input',
                    $('.filters th').eq($(api.column(colIdx).header()).index())
                )
                    .off('keyup change')
                    .on('change', function (e) {
                        // Get the search value
                        $(this).attr('title', $(this).val());
                        var regexr = '({search})'; //$(this).parents('th').find('select').val();

                        // var cursorPosition = this.selectionStart;
                        // Search the column for that value
                        api
                            .column(colIdx)
                            .search(
                                this.value != ''
                                    ? regexr.replace('{search}', '(((' + this.value + ')))')
                                    : '',
                                this.value != '',
                                this.value == ''
                            )
                            .draw();
                    })
                    .on('keyup', function (e) {
                        e.stopPropagation();

                        $(this).trigger('change');
                        $(this)
                            .focus()[0]
                            // .setSelectionRange(cursorPosition, cursorPosition);
                    });

                }else{
                  var column = this;
                  var cellId = `#${cell.text()}`
                  $(cell).html(`
                    <select class="border border-gray-300 dark:border-gray-600 dark:bg-stone-800 rounded text-sm font-normal placeholder:font-normal w-full" id="${cell.text()}">
                      <option value="">All</option>
                    </select>
                  `)
                  .on('change', function(d){
                    var temp = $(this).find(":selected").val()
                    var val = $.fn.dataTable.util.escapeRegex(temp);
                    column.search( val ? val : '', true, false ).draw();
                  })

                  api.column(colIdx).data().unique().sort().each(function(d, j){
                    let optionData = d.replace(/<[^>]*>?/gm, '').trim()
                    $(cellId).append('<option value="' + optionData + '">' + optionData + '</option>');
                    // setTimeout(() => {
                    // }, 1000
                    // )
                  })

                  
                }
              }else{
                $(cell).html("")
              }
          });
},
});