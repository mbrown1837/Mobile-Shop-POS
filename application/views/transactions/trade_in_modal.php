<!-- Trade-In Modal -->
<div class="modal fade" id="tradeInModal" data-backdrop="static" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center"><i class="fa fa-exchange"></i> Add Trade-In Device</h4>
            </div>
            <div class="modal-body">
                <form id="tradeInForm">
                    <div class="form-group">
                        <label for="tradeInBrand">Brand <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tradeInBrand" placeholder="e.g., Samsung, Apple, Xiaomi" required>
                    </div>

                    <div class="form-group">
                        <label for="tradeInModel">Model <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tradeInModel" placeholder="e.g., Galaxy S21, iPhone 12" required>
                    </div>

                    <div class="form-group">
                        <label for="tradeInImei">IMEI Number (Optional)</label>
                        <input type="text" class="form-control" id="tradeInImei" placeholder="15-digit IMEI (optional)" maxlength="15">
                        <span class="help-block text-muted">Leave empty if device has no IMEI or is not trackable</span>
                    </div>

                    <div class="form-group">
                        <label for="tradeInCondition">Condition <span class="text-danger">*</span></label>
                        <select class="form-control" id="tradeInCondition" required>
                            <option value="">Select Condition</option>
                            <option value="excellent">Excellent - Like new, no scratches</option>
                            <option value="good">Good - Minor scratches, fully functional</option>
                            <option value="fair">Fair - Visible wear, fully functional</option>
                            <option value="poor">Poor - Heavy wear, may have issues</option>
                            <option value="faulty">Faulty - Not working properly</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tradeInStorage">Storage/Specs (Optional)</label>
                        <input type="text" class="form-control" id="tradeInStorage" placeholder="e.g., 128GB, 6GB RAM">
                    </div>

                    <div class="form-group">
                        <label for="tradeInColor">Color (Optional)</label>
                        <input type="text" class="form-control" id="tradeInColor" placeholder="e.g., Black, Blue">
                    </div>

                    <div class="form-group">
                        <label for="tradeInValue">Trade-In Value (PKR) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="tradeInValue" placeholder="Enter value in PKR" min="0" step="100" required>
                        <span class="help-block text-muted">This amount will be deducted from the total</span>
                    </div>

                    <div class="form-group">
                        <label for="tradeInNotes">Notes (Optional)</label>
                        <textarea class="form-control" id="tradeInNotes" rows="3" placeholder="Any additional notes about the device condition, accessories included, etc."></textarea>
                    </div>

                    <div class="alert alert-info">
                        <strong>Note:</strong> The trade-in value cannot exceed the transaction total.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="saveTradeInBtn">
                    <i class="fa fa-check"></i> Add Trade-In
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
