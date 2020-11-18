<div id="gantt"></div>

@push('head')
<link rel="stylesheet" href="{{ asset('assets/vendor/dhtml/dhtmlxgantt.css?v=6.1.2') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/dhtml/skins/dhtmlxgantt_meadow.css?v=6.1.2') }}">
<!-- <link rel="stylesheet" href="{{ asset('assets/vendor/dhtml/skins/dhtmlxgantt_skyblue.css?v=6.1.2') }}"> -->


<style>
	.gantt_resource_task .gantt_task_content {
		color:inherit;
	}
	.gantt_resource_task .gantt_task_progress {
		background-color:rgba(33,33,33,0.3);
	}
	.gantt_cell:nth-child(4) .gantt_tree_content{
		border-radius: 16px;
		width: 100%;
		height: 80%;
		margin: 5% 0;
		line-height: 230%;
	}
	.gantt_bar_task {
		border: 0px;
	}
	.weekend{
		background: #F0DFE5 !important;
	}
	.gantt_task_cell.week_end {
		background-color: #EFF5FD;
	}

	.gantt_task_row.gantt_selected .gantt_task_cell.week_end {
		background-color: #F8EC9C;
	}

	.gantt_cal_larea {
		padding-bottom: 10px;
	}
	
	.complete_button {
		margin-top: 1px;
		background-image: url("{{ asset('assets/images/complete.png') }}");
		width: 20px;
	}

	.dhx_btn_set.complete_button_set {
		background: #ACCAAC;
		color: #454545;
		border: 1px solid #94AD94;
	}

	.completed_task {
		border: 1px solid #94AD94;
	}

	.completed_task .gantt_task_progress {
		background: #ACCAAC;
	}

	.dhtmlx-completed {
		border-color: #669e60;
	}

	.dhtmlx-completed div {
		background: #81c97a;
	}

	.updColor{
    	font-weight: 700;
  	}

	.material_line {
		background-color: #00642c;
	}

	.mrp_line {
		background-color: #2285dd;
	}
</style>
@endpush

@push('bottom')
<script src="{{ asset('assets/vendor/dhtml/dhtmlxgantt.js?v=6.1.2') }}"></script>
<script src="{{ asset('assets/vendor/dhtml/ext/dhtmlxgantt_marker.js?v=6.1.2') }}"></script>
<script src="{{ asset('assets/vendor/dhtml/ext/dhtmlxgantt_multiselect.js') }}"></script>
<script>

	var setHeight = function () {
		var height = ((window.innerHeight > 0) ? window.innerHeight : this.screen.height) - 1;
		var topOffset = 325;
        
		height = height - topOffset;

        if (height < 1) height = 1;
		
		$("#gantt").css('height', (height) + "px");
		
	};

	$(window).ready(setHeight);
    $(window).on("resize", setHeight);

    function formatNumber(num) {
    	num = parseFloat(num);
    	return num.toFixed(2);
    }

	gantt.config.scale_unit = "month";
	gantt.config.date_scale = "%M, %Y";
	<?php
		if($param['scale'] == 1) {
			echo 'gantt.config.min_column_width = 30;';
			echo 'gantt.config.scale_height = 20 * 4;';
		} else {
			echo 'gantt.config.min_column_width = 100;';
			echo 'gantt.config.scale_height = 20 * 3;';
		}
	?>		

	gantt.config.grid_width = 420;
	gantt.config.open_tree_initially = true;

	gantt.config.show_progress = 1;
	gantt.config.drag_project = 0;
	gantt.config.drag_links = 0;
	gantt.config.drag_resize = 0;
	gantt.config.drag_progress = 0;
	gantt.config.drag_move = 0;

	<?php
	if($param['type']==1) {
		echo 'gantt.config.order_branch = true;';
		echo 'gantt.config.order_branch_free = true;';
	}
	?>

	gantt.config.work_time = true;

	gantt.templates.task_cell_class = function (task, date) {
		if (!gantt.isWorkTime(date))
			return "week_end";
		return "";
	};

	gantt.ignore_time = function(date){
	   if(date.getDay() == 0 || date.getDay() == 6)
	      return true;
	};

	<?php
	foreach($data['holiday'] as $day) {
		echo "gantt.setWorkTime({date:new Date(".date('Y',strtotime($day->date)).", ".(date('n',strtotime($day->date))-1).", ".date('j',strtotime($day->date))."), hours:false});\n";
	}
	?>


	var weekScaleTemplate = function (date) {
		var dateToStr = gantt.date.date_to_str("%d %M");
		var endDate = gantt.date.add(gantt.date.add(date, 1, "week"), -1, "day");
		return dateToStr(date) + " - " + dateToStr(endDate);
	};	

	var capacityScaleTemplate = function (date) {
		return "<strong>48/32</strong>";
	};	

	var daysStyle = function(date){
		var dateToStr = gantt.date.date_to_str("%D");
		if (dateToStr(date) == "Sun"||dateToStr(date) == "Sat")  return "weekend";
	
		return "";
	};

	gantt.config.subscales = [
		{unit: "week", step: 1, template: weekScaleTemplate},
		<?php
		if($param['scale'] == 1)
		echo '{unit: "day" , step: 1, date:"%d", css:daysStyle },';
		?>		
	];

	var date_to_str = gantt.date.date_to_str(gantt.config.task_date);
	var today = new Date();
	gantt.addMarker({
		start_date: today,
		text: "Today",
		title: "Today: " + date_to_str(today)
	});

	var nextWeek = new Date();
	nextWeek.setDate(nextWeek.getDate() + 7);
	gantt.addMarker({
		start_date: nextWeek,
		css: "material_line",
		text: "Material Preparation",
		title: "Material Preparation: " + date_to_str(nextWeek)
	});
	
	var nextMonth = new Date();
	nextMonth.setDate(nextMonth.getDate() + 35);
	gantt.addMarker({
		start_date: nextMonth,
		css: "mrp_line",
		text: "MRP",
		title: "MRP: " + date_to_str(nextMonth)
	});
	
	// gantt.config.start_date = new Date({{ date('Y',strtotime($param['start_date'])) }},{{ date('m',strtotime($param['start_date'])) }},{{ date('d',strtotime($param['start_date'])) }});
	// gantt.config.end_date = new Date({{ date('Y',strtotime($param['end_date'])) }},{{ date('m',strtotime($param['end_date'])) }},{{ date('d',strtotime($param['end_date'])) }});

	gantt.serverList("Line", [
		{key: 999, label: "", backgroundColor:"#987E51", textColor:"#FFF"},
	<?php 
	foreach($lines as $line) {
		echo "{key: ".$line->id.", label: '".$line->name."',backgroundColor:'#".$line->background_color."', textColor:'#".$line->text_color."'},\n";
	}
	?>
	]);

	var labels = gantt.locale.labels;
	labels.column_owner = labels.section_owner = "Line";

	function byId(list, id) {
		for (var i = 0; i < list.length; i++) {
			if (list[i].key == id)
				return list[i].label || "";
		}
		return "";
	}

	function formatNumber(num) {
	  	return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
	}


	gantt.config.columns = [
		{name: "item_no", tree: true, label: "Item#", width: 150},
		{name: "description", label: "Item", width: 240},
		{name: "qty", label: "Qty", width: 80, align:"right", template: function(item) {return formatNumber(item.qty);}},
		{name: "owner", width: 80, align: "center", template: function (item) {return byId(gantt.serverList('Line'), item.owner_id)}},
		// {name: "po_no", width: 80},
		// {name: "ship_date", label: "Ship Date", width: 80, align: "center", resize: true},
	];


	gantt.locale.labels["complete_button"] = "Set to Final";
	gantt.config.buttons_right = ["complete_button"];

	gantt.locale.labels["section_production_order"] = "Production Order";
	gantt.locale.labels["section_code"] = "Item FG";
	gantt.locale.labels["section_name"] = "Name";
	gantt.locale.labels["section_details"] = "Details";
	
	gantt.config.lightbox.sections = [
		{name: "production_order", height: 28, map_to: "production_order", type: "textarea", focus: true},
		{name: "code", height: 28, map_to: "item_no", type: "textarea", focus: true},
		{name: "name", height: 28, map_to: "description", type: "textarea"},
		{name: "details", height: 128, type: "template", map_to: "start_date_template"}
	];

	gantt.attachEvent("onBeforeLightbox", function (id) {
		var task = gantt.getTask(id);
		var options = { year: 'numeric', month: 'short', day: '2-digit' };

		task.start_date_template = 
			"Start Date: <strong>"+task.start_date.toLocaleDateString("en-US", options)+" "+task.start_date.toLocaleTimeString('en-US')+"</strong><br/>"+
			"End Date: <strong>"+task.end_date.toLocaleDateString("en-US", options)+" "+task.end_date.toLocaleTimeString('en-US')+"</strong><hr/>"+
			"Customer: <strong>"+task.customer+"</strong><br/>"+
			"PO #: <strong>"+task.po_no+"</strong><br/>"+
			"Ship Date: <strong>"+task.ship_date+"</strong>";
		return true;
	});

	gantt.attachEvent("onLightboxButton", function (button_id, node, e) {
		if (button_id == "complete_button") {

			var id = gantt.getState().lightbox;
			var task = gantt.getTask(id);
			var url = "{{MITBooster::mainpath('final')}}";
			var change_id = id;
			if(task.parent_id != null) {
				url = "{{MITBooster::mainpath('final-header')}}";
				change_id = task.parent_id;
			}

			if (task.status == "FINAL") {
				gantt.message({text: "The task is already final!", type: "completed"});
			}else{
				$.ajax({
		            url: url,
		            type: 'GET',
		            async: false,
		            data: {
		                'id': change_id
		            },
		            contentType: 'application/json',
		            success: function (data, textStatus, jqXHR) {
    					gantt.hideLightbox();
						task.status = "FINAL";
						gantt.updateTask(id);

		                // alert('Succeed');
		                // $('#form-filter').submit();
		            },
		            error: function (jqXHR, textStatus, errorThrown) {
		                console.log('ERRORS: ' + textStatus);
		                                                
		            }
		        });
			}
		}
	});

	gantt.templates.task_class = gantt.templates.grid_row_class = gantt.templates.task_row_class = function (start, end, task) {
		if (gantt.isSelectedTask(task.id))
			return "gantt_selected";
	};

	gantt.templates.rightside_text = function(start, end, task){
		return parseFloat(task.work_hour).toFixed(2) + ' - ' + task['status'] + ' ('+Math.round(task.progress * 100)+'%)';

		//parseFloat(item.work_hour).toFixed(2);
	};

	gantt.templates.grid_row_class =
		gantt.templates.task_row_class =
			gantt.templates.task_class = function (start, end, task) {
				var css = [];
				if (task.$virtual || task.type == gantt.config.types.project)
					css.push("summary-bar");

				if(task.owner_id){
					css.push("gantt_resource_task gantt_resource_" + task.owner_id);
				}

				<?php
				if($param['type'] == 1) {
					echo 'if(task.item_no.startsWith("PO")) css.push("updColor");';	
				}
				?>

				return css.join(" ");
			};

	// gantt.addMarker({
	// 	start_date: someDate,
	// 	css: "status_line",
	// 	text: "Material Preparation",
	// 	title: "Material Preparation: " + date_to_str(start)
	// });

	gantt.attachEvent("onLoadEnd", function(){
		var styleId = "dynamicGanttStyles";
		var element = document.getElementById(styleId);
		if(!element){
			element = document.createElement("style");
			element.id = styleId;
			document.querySelector("head").appendChild(element);
		}
		var html = [];
		var resources = gantt.serverList("Line");

		resources.forEach(function(r){
			html.push(".gantt_task_line.gantt_resource_" + r.key + "{" +
				"background-color:"+r.backgroundColor+"; " +
				"color:"+r.textColor+";" +
			"}");
			if(r.key != 999) {
				html.push(".gantt_row.gantt_resource_" + r.key + " .gantt_cell:nth-child(4) .gantt_tree_content{" +
					"background-color:"+r.backgroundColor+"; " +
					"color:"+r.textColor+";" +
					"}");
			}
		});
		element.innerHTML = html.join("");
	});

	gantt.templates.task_text = function (start, end, item) {
		// return parseFloat(item.work_hour).toFixed(2);
		return "";
	};

	// gantt.templates.progress_text = function (start, end, task) {
	// 	return "<span style='text-align:center;'>" + Math.round(task.progress * 100) + "% </span>";
	// };

	//gantt.attachEvent("onRowDragStart", function(id, target, e) {
	  //  var task = gantt.getTask(id);
	    //if(task.status == "FINAL")
	    	//return false;
	    //return true;
	//});

	function closeAll()
   	{
        gantt.eachTask(function(task2close){
            if (task2close.$level == 0) { //is a project, not a task
            	gantt.close(task2close.id);
            }//endif  
        });
    }//closeAll
    
    function openAll()
    {
        gantt.eachTask(function(task2open){
            if (task2open.$level == 0) { //is a project, not a task
            	gantt.open(task2open.id);
                console.log(task2open.id);
            }//endif
        });
        
    }//openAll

	gantt.init("gantt");
	gantt.load("{{ MITBooster::mainpath('orders') }}?start_date={{$param['start_date']}}&end_date={{$param['end_date']}}&cell={{$param['cell']}}&type={{$param['type']}}");
</script>
@endpush