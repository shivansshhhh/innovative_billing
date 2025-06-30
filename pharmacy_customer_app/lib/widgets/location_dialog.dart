import 'package:flutter/material.dart';

class LocationDialog extends StatelessWidget {
  const LocationDialog({super.key});

  @override
  Widget build(BuildContext context) {
    final List<String> locations = [
      'Dehradun',
      'Delhi',
      'Mumbai',
      'Lucknow',
      'Chennai',
      'Kolkata',
      'Bengaluru',
      'Hyderabad',
      'All India',
    ];

    return AlertDialog(
      title: const Text('Choose Location'),
      content: SizedBox(
        width: double.maxFinite,
        child: ListView.builder(
          shrinkWrap: true,
          itemCount: locations.length,
          itemBuilder: (context, index) {
            return ListTile(
              title: Text(locations[index]),
              onTap: () {
                Navigator.of(context).pop(locations[index]);
              },
            );
          },
        ),
      ),
    );
  }
}
