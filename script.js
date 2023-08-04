new DataTable('#example', 
{
    processing: true,
    serverSide: true,
    serverMethod: 'post',
    ajax: {'url': 'data.php' },
    columns: [
        {data: 'name'},
        {data: 'department'},
        {data: 'position'},
        {data: 'age'},
        {data: 'startdate'},
        {data: 'salary'}
    ]
}
);