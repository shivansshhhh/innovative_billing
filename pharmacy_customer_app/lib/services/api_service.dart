import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/medicine.dart';

class ApiService {
  static const String baseUrl = "https://239e-2409-40d2-3052-a544-65d4-3687-4b9e-e9d7.ngrok-free.app/pharmacy_customer_app/api";

  static Future<List<Medicine>> searchMedicine(String searchTerm) async {
    final response = await http.get(Uri.parse('$baseUrl/search_medicine.php?search=$searchTerm'));

    if (response.statusCode == 200) {
      final data = json.decode(response.body);

      if (data['found'] == true) {
        List medicines = data['medicines'];
        return medicines.map((json) => Medicine.fromJson(json)).toList();
      } else {
        return [];
      }
    } else {
      throw Exception('Failed to load medicines');
    }
  }
}
