$(function(){
    /**
     * all response will be in below format
     * {
     *     success : boolean,
     *     data : {resource_object} or null,
     *     message : string,
     *     code : integer
     * }
     */

    var $loader = $('#loader');

    /**
     * reset the form and show it!
     */
    $('#btn-group-add').click(function(e){
        e.preventDefault();
        $('#group-form-data').each(function(){
            this.reset();
        });
        $('#btn-group-save').attr('data-method', 'POST');
        $('#group-modal').modal({ backdrop: 'static', keyboard: false }); 
        $('#group-modal').modal('show');
    });

    /**
     * sen GET request to display resource with specific id, and display it in modal form
     */
    $('#group-table').on('click', '.btn-group-edit', function(e){
        var $groupid = $(this).attr('data-id');

        e.preventDefault();
        $loader.show();

        $.get(global.baseUrl+'admin/group/'+$groupid, function(resp){
            if(resp.success){
                $('#group-form-data').each(function(){
                    this.reset();
                });

                var $group = resp.data;

                for(var a in $group){
                    $('#group_'+a).val($group[a]);
                }

                $('#btn-group-save').attr('data-method', 'PUT');
                $('#group-modal').modal({ backdrop: 'static', keyboard: false }); 
                $('#group-modal').modal('show');
            }else{
                alert(resp.message);
                if(resp.code == 401){
                    location.reload();
                }
            }

            $loader.hide();
        });
    });

    /**
     * send DELETE request to the resouce server
     */
    $('#group-table').on('click', '.btn-group-delete', function(e){
        var $groupid = $(this).attr('data-id');
        e.preventDefault();

        if(confirm('Are you sure to delete this group?')){
            $loader.show();
            $.ajax({
                url    : global.baseUrl+'admin/group/'+$groupid,
                method : 'DELETE',
                data   : {
                    id : $groupid
                },
                success : function(resp){
                    if(resp.success){
                        $('#group-row-'+$groupid).remove();
                    }else{
                        alert(resp.message);
                        if(resp.code == 401){
                            location.reload();
                        }
                    }
                    $loader.hide();
                }
            });
        }
    });

    /**
     * send POST request to save data to resource server
     * or send PUT request to update data on resource server
     * based on data-method value
     */
    $('#btn-group-save').click(function(e){
        e.preventDefault();

        var $button = $(this),
            $groupdata = $('#group-form-data').serialize(),
            $method = $(this).attr('data-method'),
            $url = ($method == 'POST') ? global.baseUrl+'admin/group' : global.baseUrl+'admin/group/'+$('#group_id').val();

        $button.prop('disabled', true);
        $button.html('saving...');
        $loader.show();

        $.ajax({
            url: $url,
            data: $groupdata,
            method : $method,
            success: function(resp){

                $button.prop('disabled', false);
                $button.html('save');
                $loader.hide();

                if(resp.success){

                    group = resp.data;

                    if($method == 'POST'){
                        /** append group to new row */
                        $('#group-table').append(
                            '<tr id="group-row-'+resp.data.id+'">'+
                                '<td>'+group.id+'</td>'+
                                '<td>'+group.name+'</td>'+     
                                '<td>'+group.permissions+'</td>'+
                                '<td class="text-center">'+
                                    '<a data-id="'+group.id+'" class="btn btn-xs btn-primary btn-group-edit" href="#"><i class="fa fa-edit fa-fw"></i>Edit</a>'+
                                    '<a data-id="'+group.id+'" class="btn btn-xs btn-danger btn-group-delete" href="#" style="margin-left: 5px"><i class="fa fa-times fa-fw"></i>Remove</a>'+
                                '</td>'+
                            '</tr>'
                        );
                    }else{
                        var $fields = $('#group-row-'+resp.data.id+' td');
                       $($fields[1]).html(group.name);
                        $($fields[2]).html(group.permissions);
                    }

                    /** reset the form and hide modal form */
                    $('#group-form-data').each(function(){
                        this.reset();
                    });
                    $('#group-modal').modal('hide');
                }else{
                    alert(resp.message);
                    if(resp.code == 401){
                        location.reload();
                    }
                }
            }
        });
    });
	$('#datatable_group').dataTable({
        stateSave: true
    });
});