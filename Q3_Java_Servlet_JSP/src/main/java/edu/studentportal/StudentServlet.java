package edu.studentportal;

import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;
import java.io.IOException;
import java.sql.*;
import java.util.*;

@WebServlet("/students")
public class StudentServlet extends HttpServlet {
    @Override protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        List<Map<String, Object>> students = new ArrayList<>();
        String sql = "SELECT s.id, s.name, s.roll_number, s.email, s.course, " +
            "f.id AS file_id, f.original_name, f.file_type " +
            "FROM students s LEFT JOIN student_files f ON f.student_id=s.id ORDER BY s.id DESC, f.id DESC";
        try (Connection con = DB.getConnection();
             PreparedStatement ps = con.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {
            Map<Integer, Map<String, Object>> byId = new LinkedHashMap<>();
            while (rs.next()) {
                int id = rs.getInt("id");
                Map<String, Object> s = byId.computeIfAbsent(id, k -> {
                    Map<String, Object> m = new LinkedHashMap<>();
                    try {
                        m.put("id", k); m.put("name", rs.getString("name"));
                        m.put("roll", rs.getString("roll_number")); m.put("email", rs.getString("email"));
                        m.put("course", rs.getString("course")); m.put("files", new ArrayList<Map<String,Object>>());
                    } catch (SQLException e) { throw new RuntimeException(e); }
                    return m;
                });
                int fileId = rs.getInt("file_id");
                if (!rs.wasNull()) {
                    @SuppressWarnings("unchecked")
                    List<Map<String, Object>> list = (List<Map<String, Object>>) s.get("files");
                    Map<String, Object> f = new LinkedHashMap<>();
                    f.put("id", fileId); f.put("name", rs.getString("original_name")); f.put("type", rs.getString("file_type"));
                    list.add(f);
                }
            }
            students.addAll(byId.values());
        } catch (Exception e) {
            throw new ServletException("Could not load student records. Check database settings and schema.", e);
        }
        request.setAttribute("students", students);
        request.getRequestDispatcher("/students.jsp").forward(request, response);
    }
}
