$(window).on("load", function() {
	if ($("#loader-container").length){
		$("#loader-container").delay(100).fadeOut('slow');
	}
});

// Global variables
var locations = [];
var $itemForm = $('#new-item-form');
var $locForm = $('#new-loc-form');
var $locSelect = $('#new-item-loc-select');
var $editor = $('#editor');
var $editLocSelect = $('#edit-loc-select');
var $del = () => $('.del.template').clone().removeClass('template hidden').click(handleDelete);
var $edit = () => $('.edit.template').clone().removeClass('template hidden').click(handleEdit);

// Retrieve all items
$.ajax('php/getAll.php', {
	dataType: 'json',
	success: r => {
		r.data.forEach(i => {
			// Populate locations[] array for later use
			if (!locations.find(l => l[0] == i.loc)) {
				locations.push([i.loc, i.locID]);
			};
			// Insert item into table
			populateTable(i);
		});
	},
	complete: () => locations.sort().forEach(l => populateLocations(l[0], l[1])),
});

// Function to populate <select> element
function populateLocations(loc, id) {
	$(`<option value="${id}">${loc} </option>`)
	.appendTo($locSelect)
	.clone().appendTo($editLocSelect);
};

// Function to insert items into table
function populateTable(item) {
	if (item.name == null)  {return};
	$(`<tr id="inv-item-${item.id}">
		<td class="name">${item.name}</td>
		<td id=loc-${item.locID} class="loc">${item.loc}</td>
		<td class="stock">${item.stock}</td> </tr>`
	).append($(`<td></td>`).append($del).append($edit))
	.appendTo($('table'));
};

// Deletion handler
function handleDelete(e) {
	if (confirm('Confirm deletion.')) {
		let $row = $(e.target).closest('tr');
		let id = $row.attr('id').split('-')[2];
		$.ajax('php/delete-item.php', {
			data: {id: id},
			success: () => {
				$row.remove();
				alert('Item deleted.');
			},
			error: () => alert('Something went wrong.'),
		});
	}
};

// Edition handler
function handleEdit(e) {
	let $row = $(e.target).closest('tr');
	let id = $row.attr('id').split('-')[2];
	$editor.removeClass('hidden');
	$('#edit-name-field').val($row.find('.name').text());
	$('#edit-stock-field').val($row.find('.stock').text());
	$('#edit-loc-select').val($row.find('.loc').attr('id').split('-')[1]);
	$('#edit-id-field').val(id);
};

$('#edit-item-form').submit(e => {
	e.preventDefault();
	let data = $(e.target).serialize();
	$.ajax('php/edit-item.php', {
		data: data,
		success: r => {
			let $row = $(`#inv-item-${r.data.id}`);
			$row.find('.name').text(r.data.name);
			$row.find('.loc').attr('id', `loc-${r.data.locID}`).text(r.data.loc);
			$row.find('.stock').text(r.data.stock);
			$editor.addClass('hidden');
		},
		error: () => alert('Something went wrong.')
	});
});

$('#cancel-edit').click(e => $editor.addClass('hidden'));

// Insert new item
$itemForm.submit(e => {
	e.preventDefault();			// Prevent form reloading page
	let data = $(e.target).serialize();
	e.target.reset();			// Clear form
	$.ajax('php/new-item.php', {
		data: data,
		success: r => populateTable(r.data),
		error: r => alert('Something went wrong'),
	});
});

// Insert new location
$locForm.submit(e => {
	e.preventDefault();			// Prevent form reloading page
	let data = $(e.target).serialize();
	console.log(data);
	$.ajax('php/new-location.php', {
		data: data,
		dataType: 'json',
		success: r => {
			populateLocations(r.data.name, r.data.id);
			alert(`${r.data.name} added successfuly`);
			e.target.reset();	// Clear form
		},
		error: r => alert(`Something went wrong: ${r.responseJSON.status.description}`),
	});
});

//=== Form validation ===
function resetCSS(elem) {
	$(elem)
	.css('border-color', '#ced4da')
	.css('animation', '');
};

$('form *').on('invalid', e => {
	$(e.target)
		.css('border-color', 'red')
		.css('animation', 'shake .25s');
	setTimeout(resetCSS, 250, e.target);
});
//=== Form validation ===
