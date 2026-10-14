package edu.studentportal;

import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;
import java.io.*;
import java.nio.file.*;
import java.sql.*;

@WebServlet("/file")
public class FileServlet extends HttpServlet {
    // Change this to the absolute uploads folder used by Q2 on your machine.
    private static final Path UPLOAD_ROOT = Paths.get("C:/xampp/htdocs/student-portal-q2/uploads").toAbsolutePath().normalize();

    @Override protected void doGet(HttpServletRequest request, HttpServletResponse response) throws IOException {
        int fileId;
        try { fileId = Integer.parseInt(request.getParameter("id")); }
        catch (Exception e) { response.sendError(400, "Invalid file ID."); return; }

        String relativePath;
        String originalName;
        String mime;
        try (Connection con = DB.getConnection();
             PreparedStatement ps = con.prepareStatement(
                 "SELECT file_path, original_name, file_type FROM student_files WHERE id=?")) {
            ps.setInt(1, fileId);
            try (ResultSet rs = ps.executeQuery()) {
                if (!rs.next()) { response.sendError(404, "File not found."); return; }
                relativePath = rs.getString("file_path");
                originalName = rs.getString("original_name");
                mime = rs.getString("file_type");
            }
        } catch (Exception e) {
            response.sendError(500, "Could not retrieve file metadata."); return;
        }

        if (!"application/pdf".equals(mime) && !"image/jpeg".equals(mime) && !"image/png".equals(mime)) {
            response.sendError(415, "Unsupported file type."); return;
        }
        // The database path is treated only as a stored filename; prevent path traversal.
        String storedName = Paths.get(relativePath.replace("\\", "/")).getFileName().toString();
        Path path = UPLOAD_ROOT.resolve(storedName).normalize();
        if (!path.startsWith(UPLOAD_ROOT) || !Files.isRegularFile(path)) {
            response.sendError(404, "Stored file is missing."); return;
        }
        response.setContentType(mime);
        response.setHeader("X-Content-Type-Options", "nosniff");
        response.setHeader("Content-Disposition", "inline; filename=\"" +
            originalName.replace("\"", "") + "\"");
        response.setContentLengthLong(Files.size(path));
        try (InputStream in = Files.newInputStream(path); OutputStream out = response.getOutputStream()) {
            in.transferTo(out);
        }
    }
}
