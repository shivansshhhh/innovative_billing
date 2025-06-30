import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:geolocator/geolocator.dart';
import 'package:url_launcher/url_launcher.dart';



class SearchMedicinePage extends StatefulWidget {
  @override
  _SearchMedicinePageState createState() => _SearchMedicinePageState();
}

class _SearchMedicinePageState extends State<SearchMedicinePage> {
  TextEditingController _searchController = TextEditingController();
  List _medicines = [];
  bool _loading = false;

  double? userLat;
  double? userLng;

  @override
  void initState() {
    super.initState();
    getUserLocation();
  }

  Future<void> getUserLocation() async {
    LocationPermission permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
    }

    if (permission == LocationPermission.deniedForever || permission == LocationPermission.denied) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Location permission denied. Cannot fetch distances.'))
      );
      return;
    }

    Position position = await Geolocator.getCurrentPosition(desiredAccuracy: LocationAccuracy.high);
    userLat = position.latitude;
    userLng = position.longitude;
  }

  Future<void> searchMedicines(String query) async {
    if (query.isEmpty) return;

    setState(() {
      _loading = true;
    });

    var url = Uri.parse(
      'https://239e-2409-40d2-3052-a544-65d4-3687-4b9e-e9d7.ngrok-free.app/pharmacy_customer_app/api/search_medicine.php?search=$query&lat=$userLat&lng=$userLng'
    );
    var response = await http.get(url);

    if (response.statusCode == 200) {
      var data = json.decode(response.body);
      if (data['found'] == true) {
        setState(() {
          _medicines = data['medicines'];
          _loading = false;
        });
      } else {
        setState(() {
          _medicines = []; // triggers FutureBuilder for alternatives
          _loading = false;
        });
      }
    } else {
      setState(() {
        _medicines = [];
        _loading = false;
      });
    }
  }

  Future<List> fetchAlternatives() async {
    var url = Uri.parse('https://239e-2409-40d2-3052-a544-65d4-3687-4b9e-e9d7.ngrok-free.app/api/alternative_medicines.php?search=${_searchController.text}');
    var response = await http.get(url);

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      return [];
    }
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Widget buildMedicineCard(Map medicine) {
  return Card(
    margin: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
    elevation: 4,
    child: InkWell(
      onTap: () async {
        final lat = medicine['store_lat'];
        final lng = medicine['store_lng'];
        print("Lat: $lat, Lng: $lng");
        print('Launching map with coordinates: $lat, $lng');
        
        final uri = Uri.parse('https://www.google.com/maps/search/?api=1&query=$lat,$lng');

        await launchUrl(uri, mode: LaunchMode.externalApplication);{

        //if (await canLaunchUrl(url)) {
         } //await launchUrl(url, mode: LaunchMode.externalApplication);
          {
          //print("Could not launch Google Maps");
        }
      },
      child: ListTile(
        title: Text(medicine['name'], style: TextStyle(fontWeight: FontWeight.bold)),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Store: ${medicine['store_name'] ?? 'N/A'}'),
            Text('Distance: ${medicine['distance_km'] ?? '--'} km'),
            Text('Stock: ${medicine['stock']} | ₹${medicine['price']}'),
          ],
        ),
        trailing: Text('Expiry: ${medicine['expiry_date']}'),
      ),
    ),
  );
}



  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Search Medicine'),
        backgroundColor: Colors.green,
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _searchController,
                    decoration: InputDecoration(
                      hintText: 'Enter medicine name...',
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                  ),
                ),
                SizedBox(width: 10),
                ElevatedButton(
                  onPressed: () => searchMedicines(_searchController.text),
                  child: Text('Search'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.green,
                  ),
                ),
              ],
            ),
          ),
          Expanded(
            child: _loading
                ? Center(child: CircularProgressIndicator())
                : _medicines.isEmpty
                    ? FutureBuilder(
                        future: fetchAlternatives(),
                        builder: (context, snapshot) {
                          if (snapshot.connectionState == ConnectionState.waiting) {
                            return Center(child: Text('Checking alternatives...'));
                          } else if (snapshot.hasError) {
                            return Center(child: Text('Error fetching alternatives.'));
                          } else if (!snapshot.hasData || (snapshot.data as List).isEmpty) {
                            return Center(child: Text('No alternatives found.'));
                          } else {
                            List alternatives = snapshot.data as List;
                            return Column(
                              children: [
                                Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child: Text('No exact match found. Here are some alternatives:',
                                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                                ),
                                Expanded(
                                  child: ListView.builder(
                                    itemCount: alternatives.length,
                                    itemBuilder: (context, index) {
                                      return buildMedicineCard(alternatives[index]);
                                    },
                                  ),
                                ),
                              ],
                            );
                          }
                        },
                      )
                    : ListView.builder(
                        itemCount: _medicines.length,
                        itemBuilder: (context, index) {
                          return buildMedicineCard(_medicines[index]);
                        },
                      ),
          ),
        ],
      ),
    );
  }
}
