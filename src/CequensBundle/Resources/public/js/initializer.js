$(function() {
	/*$.ajax({
		type: 'GET',
		async:false,
		dataType: 'json',
		url: applicationBaseUrl+'/portal/workflow/',
		data: {},
		success: function(settings) {

		},
		error: function(response) {

		}
	});*/
	getWorkflowDetails();


});

function objectsWireLabel(){
    var result = $("<div></div>");
    var select = $("<select></select>");
    select.append("<option>1 - N</option>");
    select.append("<option>N - N</option>");
    result.append(select);
    return result.html();
}

function getWorkflowDetails()
{
	/*var data = [
		{
			module: "say",
			label: "Say",
			metadata: {
				id: 1,
				name: "Client"
			},
			position:{
				top: 20,
				left: 40
			}
		},
		{
			module: "play",
			label: "Name",
			metadata: {
				id: 1,
				name: "Name"
			},
			position:{
				top: 90,
				left: 100
			}
		}
	];*/
	$('.workflow_builder').LoadingOverlay('show',{
		image : "",
		custom: "<div class=\"progress-circle-indeterminate\" data-color=\"danger\"></div>"
	});
	var data = [];
	var adapterId = $('#adapter_id').val();
	$.ajax({
		url: applicationBaseUrl+"/portal/workflow/app/"+adapterId+"/steps",
		async:false,
		type: "GET",
		dataType: "JSON",
		success: function(response){
			data = response;
			$("#demo").jsplumb_editor({
				modules:data,
				buttons: {

					/*search: {
                        label: "Search",
                        action: function(editor){
                            alert("search");
                        }
                    }*/
				},
				wireLabel: function(info, sourceContainer, targetContainer){
					var connection = info.connection;
					var result = false;
					if((sourceContainer.module == "object") && (targetContainer.module == "object")){
						result = {
							label: objectsWireLabel()
						};
					}
					return result;
				},
				save:{
					url: applicationBaseUrl+"/portal/workflow/builder/save"
				},
				load:{
					//url: applicationBaseUrl+"/portal/workflow/app/"+$('#adapter_id').val(),
					url: applicationBaseUrl+"/portal/workflow/app/"+$('#adapter_id').val()+"/modules",
					/*data:
                        {
                            url: applicationBaseUrl+"/portal/workflow/app/4",
                        }*/

					/*{
                        containers: getWorkflowDetails(),
                        connections: [],
                    }*/
				},
			});
			$(".card").card({});
		},
		error: function(response){

		}
	});
	return data;
}