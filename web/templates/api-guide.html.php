<h1>API Guide</h1>
<p>This page provides an overview of the available API endpoints for Open Trashmail, including usage examples and explanations.</p>

<h2>General Endpoints</h2>
<table>
  <thead>
    <tr><th>Endpoint</th><th>Method</th><th>Explanation</th></tr>
  </thead>
  <tbody>
    <tr><td><code>/rss/[email-address]</code></td><td>GET</td><td>Renders RSS XML for RSS clients to render emails.</td></tr>
    <tr><td><code>/api/raw/[email-address]/[id]</code></td><td>GET</td><td>Returns the raw email of the address. Output can be large for emails with attachments.</td></tr>
    <tr><td><code>/api/attachment/[email-address]/[attachment-id]</code></td><td>GET</td><td>Returns the attachment with the correct mime type as header.</td></tr>
    <tr><td><code>/api/delete/[email-address]/[id]</code></td><td>DELETE</td><td>Deletes a specific email message and its attachments.</td></tr>
    <tr><td><code>/api/deleteaccount/[email-address]</code></td><td>DELETE</td><td>Deletes all messages and attachments of this email account.</td></tr>
  </tbody>
</table>

<h2>JSON API</h2>
<table>
  <thead>
    <tr><th>Endpoint</th><th>Method</th><th>Explanation</th></tr>
  </thead>
  <tbody>
    <tr><td><code>/json/[email-address]</code></td><td>GET</td><td>Returns an array of received emails with links to attachments and the parsed text-based body of the email. If <code>ADMIN</code> email is entered, returns all emails of all accounts.</td></tr>
    <tr><td><code>/json/[email-address]/[id]</code></td><td>GET</td><td>Returns all the data of a received email, including raw and HTML body. Can be large if there are attachments.</td></tr>
    <tr><td><code>/json/listaccounts</code></td><td>GET</td><td>If <code>SHOW_ACCOUNT_LIST</code> is enabled, returns an array of all email addresses that have received at least one email.</td></tr>
  </tbody>
</table>

<h2>Example: Get Emails for an Address</h2>
<pre><code class="language-bash">curl http://yourdomain.com/json/test@example.com</code></pre>

<h2>Example: Get Raw Email</h2>
<pre><code class="language-bash">curl http://yourdomain.com/api/raw/test@example.com/1234567890</code></pre>

<h2>Example: Delete an Email</h2>
<p>To delete an email, use the <code>DELETE</code> method:</p>
<pre><code class="language-bash">curl -X DELETE http://yourdomain.com/api/delete/test@example.com/1234567890</code></pre>

<h2>Authentication (if enabled)</h2>
<p>If a password is set in the configuration, you must provide it via form, POST/GET variable <code>password</code> or HTTP header <code>PWD</code>:</p>
<pre><code class="language-bash">curl -H "PWD: yourpassword" http://yourdomain.com/json/test@example.com</code></pre>

<h2>Webhook</h2>
<p>If <code>WEBHOOK_URL</code> is set in the config, Open Trashmail will send a POST request to this URL with the JSON data of the email as the body. This can be used to integrate with your own projects.</p>

<p>For more details, see the <a href="https://github.com/x1-2023/PrivateTrashMail#readme" target="_blank">README</a>.</p>
<script src="/js/prism.js"></script> 