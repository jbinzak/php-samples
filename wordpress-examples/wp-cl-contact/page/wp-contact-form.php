<?php
/**
 * Created by IntelliJ IDEA.
 * User: johnbinzak
 * Date: 11/1/18
 * Time: 4:30 PM
 */
?>


<div class="cloeve-contact-item">
    <h3 class="cloeve-contact-title">Let's Get Started Today</h3>
    <form action="javascript:cloeveContactFormSubmit();" class="cloeve-contact-form">
        <div class="form-group">
            <label for="contact_name">Full name</label>
            <input type="text" class="form-control" id="contact_name" name="contact_name" placeholder="Jane Smith" required>
        </div>
        <div class="form-group">
            <label for="contact_email">Email address</label>
            <input type="email" class="form-control" id="contact_email" name="contact_email" placeholder="name@example.com" required>
        </div>
        <div class="form-group">
            <label for="contact_topics">Topics (multiple select)</label>
            <select multiple class="form-control" id="contact_topics" name="contact_topics[]" required>
                <option value="technology">Technology</option>
                <option value="marketing">Content Marketing</option>
                <option value="ui_ux">UI/UX</option>
                <option value="integrations">Integrations</option>
                <option value="insights">Insights</option>
            </select>
        </div>
        <div class="form-group">
            <label for="contact_message">Message</label>
            <textarea class="form-control" id="contact_message" name="contact_message" rows="3" required></textarea>
        </div>
        <button type="submit" id="cloeve_contact_btn_submit" class="btn btn-primary btn-block cloeve-contact-btn-submit">Submit</button>
        <div class="cloeve-contact-message"><h5>Success!</h5></div>
    </form>
</div>
