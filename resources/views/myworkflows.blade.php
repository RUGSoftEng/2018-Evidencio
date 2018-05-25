@extends('layouts.app')

@section('content')

@include('partials.sidebar')

    <!--<script defer src="https://use.fontawesome.com/releases/v5.0.9/js/all.js" integrity="sha384-8iPTk2s/jMVj81dnzb/iFR2sdA7u06vHJyyLlAd4snFpCl/SnyUjRrbdJsw1pGIl" crossorigin="anonymous"></script>-->

<div class="container-fluid justify-content-center">

<div class="card">

    <div class="card-header">
        <h3 style="display: inline-block;">My Workflows</h3>
    <a style= "float: right;" class="btn btn-primary" href="{{ route('designer') }}"><i class="fo-icon icon-plus">&#xe804;</i> Create New Workflow</a>
    </div>

    <div class="card-body">
    
    
        
    <div class=myTable>
        
<div class="btn-group btn-group-toggle" style= "float: right;" data-toggle="buttons">
   Show: 
  <label class="btn btn-outline-primary active  btn-sm"  id="optionAll">
    <input type="radio" name="options" autocomplete="off" checked> All
  </label>
  <label class="btn btn-outline-primary  btn-sm"  id="optionDraft">
    <input type="radio" name="options" autocomplete="off"> Draft
  </label>
  <label class="btn btn-outline-primary  btn-sm" id="optionApproved">
    <input type="radio" name="options" autocomplete="off"> Approved
  </label>
  <label class="btn btn-outline-primary  btn-sm" id="optionPending">
    <input type="radio" name="options" autocomplete="off"> Pending
  </label>
  <label class="btn btn-outline-primary  btn-sm"  id="optionRejected">
    <input type="radio" name="options" autocomplete="off"> Rejected
  </label>
</div>

        <table id="workflowTable" align="center" class="table table-striped">
            <thead>
                <tr>
                    <th scope="col" style="width: 25%;">Name</th>
                    <th scope="col" style="width: 15%;">Date Created</th>
                    <th scope="col" style="width: 15%;">Date Modified</th>
                    <th scope="col" style="width: 25%;">Status</th>
                    <th scope="col" style="width: 20%;"></th>
                </tr>
            </thead>
            <tbody>
                <tr class="btnDelete" data-id="1">
                    <td>Cold</td>
                    <td>04.04.2018</td> 
                    <td>04.04.2018</td> 
                    <td>Pending</td>
                    <td>
                        <div>
                            <button type="button" class="btn btn-primary">View </button>
                            <button type="button" class="btn btn-primary">Edit</button>
                            <button class="btnDelete btn btn-primary">Delete</button>
                        </div>
                    </td>
                </tr> 
                <tr class="btnDelete" data-id="2">
                    <td>Cancer</td>
                    <td>05.04.2018</td> 
                    <td>05.04.2018</td> 
                    <td>Approved</td>
                    <td>
                        <div>
                            <button type="button" class="btn btn-primary">View </button>
                            <button type="button" class="btn btn-primary">Edit</button>
                            <button class="btnDelete btn btn-primary">Delete</button>
                        </div>
                    </td> 
                </tr> 
            </tbody>
        </table>
                
        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Delete the Selected Workflow</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this workflow?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                        <button type="button" class="btn btn-primary" id="btnDeleteYes" href="#" data-dismiss="modal">Yes</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
        
        
    </div>
</div>


 </div>
<script>
    $(document).on('click','#optionDraft',function(){
        var table, tr, td, i;
        table = document.getElementById("workflowTable");
        tr = table.getElementsByTagName("tr");
            
        for (i = 0; i < tr.length; i++) 
        {
            td = tr[i].getElementsByTagName("td")[3];
            if (td) 
            {
                if (td.innerHTML == "Draft")
                {
                    tr[i].style.display = "";
                } 
                else 
                {
                    tr[i].style.display = "none";
                }
            } 
        }
    });
    
    $(document).on('click','#optionApproved',function(){
        var table, tr, td, i;
        table = document.getElementById("workflowTable");
        tr = table.getElementsByTagName("tr");
            
        for (i = 0; i < tr.length; i++) 
        {
            td = tr[i].getElementsByTagName("td")[3];
            if (td) 
            {
                if (td.innerHTML == "Approved")
                {
                    tr[i].style.display = "";
                } 
                else 
                {
                    tr[i].style.display = "none";
                }
            } 
        }
    });
    
    $(document).on('click','#optionPending',function(){
        var table, tr, td, i;
        table = document.getElementById("workflowTable");
        tr = table.getElementsByTagName("tr");
            
        for (i = 0; i < tr.length; i++) 
        {
            td = tr[i].getElementsByTagName("td")[3];
            if (td) 
            {
                if (td.innerHTML == "Pending")
                {
                    tr[i].style.display = "";
                } 
                else 
                {
                    tr[i].style.display = "none";
                }
            } 
        }
    });

    $(document).on('click','#optionRejected',function(){
        var table, tr, td, i;
        table = document.getElementById("workflowTable");
        tr = table.getElementsByTagName("tr");
            
        for (i = 0; i < tr.length; i++) 
        {
            td = tr[i].getElementsByTagName("td")[3];
            if (td) 
            {
                if (td.innerHTML == "Rejected")
                {
                    tr[i].style.display = "";
                } 
                else 
                {
                    tr[i].style.display = "none";
                }
            } 
        }
    });
 
    $(document).on('click','#optionAll',function(){
        var table, tr, td, i;
        table = document.getElementById("workflowTable");
        tr = table.getElementsByTagName("tr");
            
        for (i = 0; i < tr.length; i++) 
        {
            td = tr[i].getElementsByTagName("td")[3];
            if (td) 
            {
                tr[i].style.display = "";
            } 
        }
    });



    
    $('button.btnDelete').on('click', function (e) {
        e.preventDefault();
        var id = $(this).closest('tr').data('id');
        $('#deleteModal').data('id', id).modal('show');
    });

    $('#btnDeleteYes').click(function () {
        var id = $('#deleteModal').data('id');
        $('[data-id=' + id + ']').remove();
        $('#deleteModal').modal('hide');
        //also delete from database
    });

</script>


<style>

    .myTable
    {
        padding-top: 50px;
        padding-right: 80px;
        padding-bottom: 50px;
        padding-left: 80px;
    }
</style>

@endsection