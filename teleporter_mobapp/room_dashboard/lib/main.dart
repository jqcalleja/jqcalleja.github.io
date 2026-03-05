import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:url_launcher/url_launcher.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        useMaterial3: true,

        // Equivalent to your CSS variables
        colorScheme: const ColorScheme.light(
          primary: Color(0xFF580000),      // --color-primary
          secondary: Color(0xFF160070),    // --color-secondary
          surface: Color(0xFFF4F6F9),      // --color-background
        ),

        scaffoldBackgroundColor: const Color(0xFFF4F6F9),

        appBarTheme: const AppBarTheme(
          backgroundColor: Color(0xFF580000),
          foregroundColor: Colors.white,
          centerTitle: true,
        ),

        elevatedButtonTheme: ElevatedButtonThemeData(
          style: ElevatedButton.styleFrom(
            backgroundColor: const Color(0xFF160070),
            foregroundColor: Colors.white,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.all(Radius.circular(12)), // --radius-lg
            ),
          ),
        ),
      ),
      home: const RoomDashboard(),
    );
  }
}

class RoomDashboard extends StatefulWidget {
  const RoomDashboard({super.key});

  @override
  State<RoomDashboard> createState() => _RoomDashboardState();
}

class RoomTile extends StatefulWidget {
  final String name;
  final VoidCallback onTap;

  const RoomTile({
    super.key,
    required this.name,
    required this.onTap,
  });

  @override
  State<RoomTile> createState() => _RoomTileState();
}

class _RoomTileState extends State<RoomTile> {
  bool _isPressed = false;

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTapDown: (_) => setState(() => _isPressed = true),
      onTapUp: (_) {
        setState(() => _isPressed = false);
        widget.onTap();
      },
      onTapCancel: () => setState(() => _isPressed = false),
      child: AnimatedScale(
        scale: _isPressed ? 0.95 : 1.0, // 🔥 Press animation
        duration: const Duration(milliseconds: 100),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          decoration: BoxDecoration(
            color: const Color(0xFF160070),
            borderRadius: BorderRadius.circular(16),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.15),
                blurRadius: _isPressed ? 4 : 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: Center(
            child: Padding(
              padding: const EdgeInsets.all(12),
              child: Text(
                widget.name,
                textAlign: TextAlign.center,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class _RoomDashboardState extends State<RoomDashboard> {
  List rooms = [];
  List filteredRooms = [];
  bool isLoading = true;

  final String jsonUrl =
      "https://raw.githubusercontent.com/jqcalleja/jqcalleja.github.io/main/teleporter/rooms.json";

  @override
  void initState() {
    super.initState();
    fetchRooms();
  }

  Future<void> fetchRooms() async {
    try {
      final response = await http.get(Uri.parse(jsonUrl));

      if (response.statusCode == 200) {
        final data = json.decode(response.body);

        setState(() {
          rooms = data["rooms"] ?? [];
          filteredRooms = rooms;
          isLoading = false;
        });
      } else {
        throw Exception("Failed to load JSON");
      }
    } catch (e) {
      setState(() => isLoading = false);

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Error loading data: $e")),
      );
    }
  }

  void filterRooms(String query) {
    setState(() {
      filteredRooms = rooms
          .where((room) =>
              room["name"].toLowerCase().contains(query.toLowerCase()))
          .toList();
    });
  }

  Future<void> openUrl(String url) async {
    final Uri uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Room Access Dashboard"),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [

            // Search Field
            TextField(
              onChanged: filterRooms,
              decoration: InputDecoration(
                hintText: "Search room...",
                prefixIcon: const Icon(Icons.search),
                filled: true,
                fillColor: Colors.white,
                contentPadding: const EdgeInsets.symmetric(vertical: 14),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(50), // pill style
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(50),
                  borderSide: const BorderSide(
                    color: Color(0xFF580000),
                    width: 2,
                  ),
                ),
              ),
            ),

            const SizedBox(height: 16),

            // Content
            Expanded(
              child: isLoading
                  ? const Center(child: CircularProgressIndicator())
                  : GridView.builder(
                      gridDelegate:
                          const SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: 2, // Square grid
                        crossAxisSpacing: 12,
                        mainAxisSpacing: 12,
                        childAspectRatio: 1, // Makes it square
                      ),
                      itemCount: filteredRooms.length,
                      itemBuilder: (context, index) {
                        final room = filteredRooms[index];

                        return RoomTile(
                          name: room["name"],
                          onTap: () => openUrl(room["url"]),
                        );
                      },
                    ),
            ),
          ],
        ),
      ),
    );
  }
}