import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/location_provider.dart';
import 'home_screen.dart';

class LocationSelectionPage extends StatelessWidget {
  final List<String> cities = [
    'Dehradun',
    'Delhi',
    'Mumbai',
    'Lucknow',
    'Chennai',
    'Kolkata',
    'Bengaluru',
    'Hyderabad',
    'All India'
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Select Your Location'),
        backgroundColor: Colors.green,
      ),
      body: ListView.builder(
        itemCount: cities.length,
        itemBuilder: (context, index) {
          return ListTile(
            leading: const Icon(Icons.location_on, color: Colors.green),
            title: Text(cities[index]),
            onTap: () {
              Provider.of<LocationProvider>(context, listen: false)
                  .setLocation(cities[index]);

              Navigator.pushReplacement(
                context,
                MaterialPageRoute(builder: (_) => const HomeScreen()),
              );
            },
          );
        },
      ),
    );
  }
}
