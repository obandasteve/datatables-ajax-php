<?php
    include 'config.php';

    //reading values
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; //row display per page
    $columnIndex = $_POST['order'][0]['column']; //column index
    $columnName = $_POST['columns'][$columnIndex]['data']; //column name
    $columnSortOrder = $_POST['order'][0]['dir']; //asc or dsc
    $searchValue = $_POST['search']['value']; //search value

    $searchArray = array();

    //search
    $searchQuery = " ";
    if($searchValue != ''){
        $searchQuery = " AND (name LIKE :name OR
            department LIKE :department OR
            position LIKE :position OR
            age LIKE :age OR
            startdate LIKE :startdate OR
            salary LIKE :salary )";
        $searchArray = array(
            'name'       =>"%$searchValue%",
            'department' =>"%$searchValue%",
            'position'   =>"%$searchValue%",
            'age'        =>"%$searchValue%",
            'startdate'  =>"%$searchValue%",
            'salary'     =>"%$searchValue%"
        );  
    }

    //total number of records without filtering
    $stmt = $conn->prepare("SELECT COUNT(*) AS allcount from staff ");
    $stmt->execute();
    $records = $stmt->fetch();
    $totalRecords = $records['allcount'];

    //total number of records with filtering
    $stmt = $conn->prepare("SELECT COUNT(*) AS allcount FROM staff WHERE 1 ".$searchQuery);
    $stmt->execute($searchArray);
    $records = $stmt->fetch();
    $totalRecordwithFilter = $records['allcount'];

    //fetch records
    $stmt = $conn->prepare("SELECT * FROM staff WHERE 1".$searchQuery." ORDER BY ".$columnName." ".$columnSortOrder." LIMIT :limit,:offset");

    //Bind Values
    foreach ($searchArray as $key=>$search) {
        $stmt->bindValue(':'.$key, $search,PDO::PARAM_STR);
    }

    $stmt->bindValue(':limit', (int)$row, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$rowperpage, PDO::PARAM_INT);
    $stmt->execute();
    $staffrecords = $stmt->fetchALL();

    $data = array();

    foreach ($staffrecords as $row){
        $data[] = array(
            "name"=>$row['name'],
            "department"=>$row['department'],
            "position"=>$row['position'],
            "age"=>$row['age'],
            "startdate"=>$row['startdate'],
            "salary"=>$row['salary']
        );
    }
    
    //response
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData" =>$data
    );

    echo json_encode($response);
    
?>