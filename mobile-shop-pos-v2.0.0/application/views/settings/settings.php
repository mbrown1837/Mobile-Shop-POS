<?php
defined('BASEPATH') OR exit('');
?>

<div class="pwell">
    <div class="row">
        <div class="col-sm-12">
            <h3><i class="fa fa-cog"></i> Shop Settings</h3>
            <p class="text-muted">Configure your shop and system preferences</p>
            <hr>
            
            <div class="row">
                <!-- Shop Information -->
                <div class="col-sm-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4><i class="fa fa-store"></i> Shop Information</h4>
                        </div>
                        <div class="panel-body">
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> <strong>Note:</strong> Shop information is configured in the <code>.env</code> file.
                            </div>
                            
                            <table class="table table-condensed">
                                <tr>
                                    <td><strong>Shop Name:</strong></td>
                                    <td><?=getenv('SHOP_NAME') ?: 'Mobile Shop POS'?></td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td><?=getenv('SHOP_ADDRESS') ?: 'Not configured'?></td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td><?=getenv('SHOP_PHONE') ?: 'Not configured'?></td>
                                </tr>
                                <tr>
                                    <td><strong>Currency:</strong></td>
                                    <td><?=getenv('CURRENCY_SYMBOL') ?: 'Rs.'?></td>
                                </tr>
                            </table>
                            
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i> To update shop information, edit the <code>.env</code> file in the root directory.
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Security Settings -->
                <div class="col-sm-6">
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <h4><i class="fa fa-lock"></i> Security Settings</h4>
                        </div>
                        <div class="panel-body">
                            <form id="passwordChangeForm">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> <strong>Change Admin Password</strong><br>
                                    For security, change the default password regularly.
                                </div>
                                
                                <div class="form-group">
                                    <label for="current_password">Current Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="new_password">New Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" 
                                           minlength="6" required>
                                    <small class="text-muted">Minimum 6 characters</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirm_password">Confirm New Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           minlength="6" required>
                                </div>
                                
                                <button type="submit" class="btn btn-warning btn-block">
                                    <i class="fa fa-key"></i> Change Password
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- System Information -->
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><i class="fa fa-info-circle"></i> System Information</h4>
                        </div>
                        <div class="panel-body">
                            <table class="table table-condensed">
                                <tr>
                                    <td><strong>Version:</strong></td>
                                    <td>v1.1.0 Simplified</td>
                                </tr>
                                <tr>
                                    <td><strong>PHP Version:</strong></td>
                                    <td><?=phpversion()?></td>
                                </tr>
                                <tr>
                                    <td><strong>Database:</strong></td>
                                    <td>MySQL <?=$this->db->version()?></td>
                                </tr>
                                <tr>
                                    <td><strong>Server:</strong></td>
                                    <td><?=$_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'?></td>
                                </tr>
                            </table>
                            
                            <div class="text-center">
                                <a href="<?=site_url('database')?>" class="btn btn-info btn-sm">
                                    <i class="fa fa-database"></i> Database Management
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Password Change Form
    $('#passwordChangeForm').on('submit', function(e) {
        e.preventDefault();
        
        const newPassword = $('#new_password').val();
        const confirmPassword = $('#confirm_password').val();
        
        if (newPassword !== confirmPassword) {
            showNotification('error', 'New passwords do not match');
            return;
        }
        
        $.ajax({
            url: '<?=site_url("settings/changePassword")?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function() {
                $('#passwordChangeForm button').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Changing...');
            },
            success: function(response) {
                if (response.status === 1) {
                    showNotification('success', response.msg);
                    $('#passwordChangeForm')[0].reset();
                } else {
                    showNotification('error', response.msg);
                }
            },
            error: function() {
                showNotification('error', 'Failed to change password');
            },
            complete: function() {
                $('#passwordChangeForm button').prop('disabled', false).html('<i class="fa fa-key"></i> Change Password');
            }
        });
    });
    
    // Notification function
    function showNotification(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade in" style="position:fixed;top:70px;right:20px;z-index:9999;min-width:300px;">' +
            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
            '<i class="fa ' + icon + '"></i> ' + message +
            '</div>');
        
        $('body').append(notification);
        
        setTimeout(function() {
            notification.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }
});
</script>
