import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'screens/location_selection_page.dart';
import 'providers/location_provider.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'models/medicine.dart';
import 'services/api_service.dart';
import 'screens/login_screen.dart';
import 'screens/register_screen.dart';
import 'screens/home_screen.dart';

void main() {
  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => LocationProvider()),
      ],
      child: const MyApp(),
    ),
  );
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Swastha App',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(primarySwatch: Colors.green),
      home: LocationSelectionPage(),
      initialRoute: '/login',
      routes: {
        '/login': (context) => const LoginScreen(),
        '/register': (context) => const RegisterScreen(), // If applicable
        '/home': (context) => HomeScreen(),
      },
    );
  }
}

class SearchMedicinePage extends StatefulWidget {
  @override
  _SearchMedicinePageState createState() => _SearchMedicinePageState();
}

class _SearchMedicinePageState extends State<SearchMedicinePage> {
  List<Medicine> _medicines = [];
  bool _isLoading = false;
  final TextEditingController _controller = TextEditingController();

  void _searchMedicines() async {
    String searchTerm = _controller.text.trim();
    if (searchTerm.isEmpty) return;

    setState(() {
      _isLoading = true;
    });

    try {
      final response = await http.get(Uri.parse('${ApiService.baseUrl}/search_medicine.php?search=$searchTerm'));

      if (response.statusCode == 200) {
        final data = json.decode(response.body);

        if (data['found'] == true) {
          List medicines = data['medicines'];
          setState(() {
            _medicines = medicines.map((json) => Medicine.fromJson(json)).toList();
          });
        } else {
          List alternatives = data['alternatives'];
          setState(() {
            _medicines = alternatives.map((json) => Medicine.fromJson(json)).toList();
          });
        }
      } else {
        print('Failed to load medicines');
      }
    } catch (e) {
      print(e);
    }

    setState(() {
      _isLoading = false;
    });
  }

  Widget _buildMedicineCard(Medicine medicine) {
    return Card(
      child: ListTile(
        title: Text(medicine.name),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Stock: ${medicine.stock}'),
            Text('Price: ₹${medicine.price}'),
            Text('Expiry: ${medicine.expiryDate}'),
          ],
        ),
        trailing: Icon(Icons.medical_services),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Search Medicines'),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            TextField(
              controller: _controller,
              decoration: InputDecoration(
                labelText: 'Enter medicine name',
                suffixIcon: IconButton(
                  icon: Icon(Icons.search),
                  onPressed: _searchMedicines,
                ),
              ),
            ),
            SizedBox(height: 20),
            _isLoading
                ? CircularProgressIndicator()
                : _medicines.isEmpty
                    ? Text('No medicines found')
                    : Expanded(
                        child: ListView.builder(
                          itemCount: _medicines.length,
                          itemBuilder: (context, index) {
                            return _buildMedicineCard(_medicines[index]);
                          },
                        ),
                      ),
          ],
        ),
      ),
    );
  }
}
