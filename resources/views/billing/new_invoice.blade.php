<div class="row">
    <div class="col-md-8 col-sm-8">
        <label for="name">@lang("Name")</label>
        <input type="text" name="name" class="form-control"/>

        <label>@lang("Remarks")</label>
        <textarea name="remarks" rows="4" class="form-control"></textarea>

        <div class="row">

            <div class="col-lg-2 col-md-2 col-sm-2">
                <label for="itemName">@lang("Item")</label>
                <input type="text" name="itemName[]" id="itemName" class="form-control input-sm"/>
            </div>
            <div class="col-md-4 col-sm-4">
                <input type="hidden" name="itemID[]" id="itemID"/>
                <label for="desc">@lang("Description")</label>
                <i class="fa fa-trash" id="deleteRow"></i>
                <input type="text" class="form-control" name="desc[]"/>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2">
                <label for="qty">@lang("Quantity")</label>
                <input type="text" class="form-control input-sm" name="qty[]"/>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2">
                <label for="price">@lang("Price")</label>
                <input type="text" class="form-control input-sm" name="price[]"/>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2">
                <label>@lang("Total")</label>
                <input type="text" class="form-control input-sm" readonly name="itemLineTotal[]"/>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-4">
        <?php $invoice_id = time() + rand(111, 999); ?>
        <div class="h4">@lang("Invoice ID"): <input type="hidden" value="<?php echo $invoice_id; ?>"/>
            <?php echo $invoice_id; ?>
        </div>
        <hr/>
        <div class="form-group">
            <input type="checkbox" name="partial"> @lang("Allow partial payment")
            <label for="min-payment">
                @lang("Minimum payment")<br/>
                <input type="text" name="min-payment"/>
            </label>
        </div>

        <div class="form-group">
            <label for="due_date">@lang("Due date")<br/>
                <input type="date" name="due_date"/>
            </label>

            <label for="tax">@lang("Tax")<br/>
                <input type="text" name="tax"/>
            </label>

        </div>

    </div>
</div>