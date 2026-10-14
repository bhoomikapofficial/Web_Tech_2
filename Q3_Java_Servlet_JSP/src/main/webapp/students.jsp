<%@ page contentType="text/html; charset=UTF-8" pageEncoding="UTF-8" %>
<%@ page import="java.util.*" %>
<%!
private String esc(Object value) {
    if (value == null) return "";
    return value.toString().replace("&", "&amp;").replace("<", "&lt;")
        .replace(">", "&gt;").replace("\"", "&quot;").replace("'", "&#x27;");
}
%>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student Profiles</title><link rel="stylesheet" href="style.css"></head><body>
<main class="container"><header><h1>Student Profiles</h1><a href="logout">Logout</a></header>
<p>Logged in as <strong><%= esc(session.getAttribute("username")) %></strong></p>
<div class="grid">
<%
List<Map<String,Object>> students = (List<Map<String,Object>>) request.getAttribute("students");
if (students == null || students.isEmpty()) {
%><p>No students found. Add a student through the PHP application (Q2) first.</p><%
} else {
for (Map<String,Object> s : students) {
%>
<section class="card">
<h2><%= esc(s.get("name")) %></h2>
<p><strong>Roll number:</strong> <%= esc(s.get("roll")) %></p>
<p><strong>Email:</strong> <%= esc(s.get("email")) %></p>
<p><strong>Course:</strong> <%= esc(s.get("course")) %></p>
<h3>Documents</h3>
<ul>
<%
List<Map<String,Object>> fs = (List<Map<String,Object>>) s.get("files");
if (fs.isEmpty()) { %><li>No documents uploaded.</li><% }
for (Map<String,Object> f : fs) {
    String type = String.valueOf(f.get("type"));
    int fid = (Integer) f.get("id");
    if (type.startsWith("image/")) {
%><li><p><%= esc(f.get("name")) %></p><img class="profile-image" src="file?id=<%= fid %>" alt="Uploaded image for <%= esc(s.get("name")) %>"></li><%
    } else {
%><li><a href="file?id=<%= fid %>" target="_blank" rel="noopener"><%= esc(f.get("name")) %> (open PDF)</a></li><%
    }
}
%></ul></section>
<% } } %>
</div></main></body></html>
