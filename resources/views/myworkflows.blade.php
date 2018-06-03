@extends('layouts.app')

@section('content')

@include('partials.sidebar')

    <div class="container-fluid">
        <div class="card">
            
            <div class="card-header">
                <h3 style="display: inline-block;">My Workflows</h3>
                <button style= "float: right;" type="button" class="btn btn-primary"  onclick="window.location='{{ url("/designer") }}'"><i class="fo-icon icon-plus">&#xe804;</i> Create New Workflow</button>
            </div>
            
            <div class="card-body">
                
                @if (count($workflows) != 0)
                <div class=myTable style="overflow-x:auto;">
                    <div class="btn-group btn-group-toggle" style= "float: right;" data-toggle="buttons">
                        Show: 
                        <label class="btn btn-outline-primary active  btn-sm"  id="optionAll">
                            <input type="radio" name="options" autocomplete="off" checked> All
                        </label>
                        <label class="btn btn-outline-primary  btn-sm"  id="optionDraft">
                            <input type="radio" name="options" autocomplete="off"> Draft
                        </label>
                        <label class="btn btn-outline-primary  btn-sm" id="optionPublished">
                            <input type="radio" name="options" autocomplete="off"> Published
                        </label>
                        <label class="btn btn-outline-primary  btn-sm" id="optionVerified">
                            <input type="radio" name="options" autocomplete="off"> Verified
                        </label>
                    </div>

                    <table id="workflowTable"  class="table" style= "float: center;">
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
                            @foreach ($workflows as $workflow)
                                <tr class="btnDelete" data-id="{{$workflow->id}}">
                                    <td>{{$workflow->title}}</td>
                                    <td>{{$workflow->created_at}}</td>
                                    <td>{{$workflow->updated_at}}</td>
                                    @if ($workflow->is_draft == 1)
                                        <td>Draft</td>
                                    @endif
                                    @if ($workflow->is_published== 1)
                                        <td>Published</td>
                                    @endif
                                    @if ($workflow->is_verified== 1)
                                        <td>Verified</td>
                                    @endif
                                    <td>
                                        <div>
                                            <button type="button" class="btn btn-primary"  onclick="window.location='{{ url("/designer?workflow=$workflow->id") }}'">Edit</button>
                                            <button class="btnDelete btn btn-primary">Delete</button>
                                        </div>
                                    </td>
                            @endforeach
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

                @else
                <div>
                    <p>You have no workflows to display at the moment.</p>
                </div>
                @endif
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
    
    $(document).on('click','#optionPublished',function(){
        var table, tr, td, i;
        table = document.getElementById("workflowTable");
        tr = table.getElementsByTagName("tr");
            
        for (i = 0; i < tr.length; i++) 
        {
            td = tr[i].getElementsByTagName("td")[3];
            if (td) 
            {
                if (td.innerHTML == "Published")
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
    
    $(document).on('click','#optionVerified',function(){
        var table, tr, td, i;
        table = document.getElementById("workflowTable");
        tr = table.getElementsByTagName("tr");
            
        for (i = 0; i < tr.length; i++) 
        {
            td = tr[i].getElementsByTagName("td")[3];
            if (td) 
            {
                if (td.innerHTML == "Verified")
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
        //$('[data-id=' + id + ']').remove();
        //$('#deleteModal').modal('hide');
        document.location.href = '/myworkflows/delete/'.concat(id);
    });

</script>


@endsection