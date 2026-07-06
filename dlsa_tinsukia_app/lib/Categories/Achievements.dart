import 'package:flutter/material.dart';
import '../NavigationBar/Sidebar.dart'; // Adjust the path if needed
import 'dart:convert';
import 'dart:typed_data';
import 'dart:async';
import 'package:http/http.dart' as http;
import 'package:cached_network_image/cached_network_image.dart';
import 'dart:io';
import '../DisplayContent/DisplayContent.dart';

class AchievementsPage extends StatefulWidget {
  const AchievementsPage({super.key});

  @override
  State<AchievementsPage> createState() => _AchievementsPageState();
}

class _AchievementsPageState extends State<AchievementsPage> {
  List<Map<String, dynamic>> achievements = [];
  bool isLoading = true;
  Map<String, int> retryCounts = {}; // Track retry attempts for each image
  
  // Base URL for API calls
  // For Android Emulator: use 10.0.2.2
  // For Physical Device: use your computer's IP address
  // For iOS Simulator: use localhost
  final String baseUrl = Platform.isAndroid 
      ? 'http://10.0.2.2:8000'     // Android Emulator
      // ? 'http://192.168.1.5:8000'  // Physical Android device use pc ipaddress
      : 'http://localhost:8000'; // iOS Simulator or other platforms

  @override
  void initState() {
    super.initState();
    fetchAchievements();
  }

  Future<void> fetchAchievements() async {
    try {
      print('Fetching data from: $baseUrl'); // Debug print
      
      // Fetch all data
      final uploadsResponse = await http.get(Uri.parse('$baseUrl/api/uploads/Retrieve'));
      final tagsResponse = await http.get(Uri.parse('$baseUrl/api/tags/Retrieve'));
      final picturesResponse = await http.get(Uri.parse('$baseUrl/api/pictures/Retrieve'));
      final linksResponse = await http.get(Uri.parse('$baseUrl/api/links/Retrieve'));
      final pdfsResponse = await http.get(Uri.parse('$baseUrl/api/pdfs/Retrieve'));

      print('Uploads Response: ${uploadsResponse.statusCode}'); // Debug print
      print('Tags Response: ${tagsResponse.statusCode}'); // Debug print
      print('Pictures Response: ${picturesResponse.statusCode}'); // Debug print
      print('Links Response: ${linksResponse.statusCode}'); // Debug print
      print('PDFs Response: ${pdfsResponse.statusCode}'); // Debug print

      if (uploadsResponse.statusCode == 200 && 
          tagsResponse.statusCode == 200 && 
          picturesResponse.statusCode == 200 &&
          linksResponse.statusCode == 200 &&
          pdfsResponse.statusCode == 200) {
        
        final List<dynamic> uploads = json.decode(uploadsResponse.body)['data'];
        final List<dynamic> tags = json.decode(tagsResponse.body)['data'];
        final List<dynamic> pictures = json.decode(picturesResponse.body)['data'];
        final List<dynamic> links = json.decode(linksResponse.body)['data'];
        final List<dynamic> pdfs = json.decode(pdfsResponse.body)['data'];

        print('Found ${uploads.length} uploads'); // Debug print
        print('Found ${tags.length} tags'); // Debug print
        print('Found ${pictures.length} pictures'); // Debug print
        print('Found ${links.length} links'); // Debug print
        print('Found ${pdfs.length} pdfs'); // Debug print

        // First, get all tags where achievement is true
        final achievementTags = tags.where((tag) => tag['achievement'] == true).toList();
        
        // Get the uploads_ids from these achievement tags
        final achievementUploadIds = achievementTags.map((tag) => tag['uploads_id'].toString()).toList();

        print('Found ${achievementTags.length} achievement tags'); // Debug print
        print('Achievement upload IDs: $achievementUploadIds'); // Debug print

        // Get all uploads that match these ids
        final filteredAchievements = uploads.where((upload) {
          final uploadId = upload['id'].toString();
          return achievementUploadIds.contains(uploadId);
        }).map((upload) {
          // Find all pictures for this upload
          final uploadPictures = pictures.where((pic) {
            final picUploadId = pic['uploads_id'].toString();
            final uploadId = upload['id'].toString();
            return picUploadId == uploadId;
          }).toList();
          
          // Find all links for this upload
          final uploadLinks = links.where((link) {
            final linkUploadId = link['uploads_id'].toString();
            final uploadId = upload['id'].toString();
            return linkUploadId == uploadId;
          }).toList();
          
          // Find all PDFs for this upload
          final uploadPdfs = pdfs.where((pdf) {
            final pdfUploadId = pdf['uploads_id'].toString();
            final uploadId = upload['id'].toString();
            return pdfUploadId == uploadId;
          }).toList();
          
          return {
            'id': upload['id'],
            'title': upload['title'],
            'upload_date': upload['upload_date'],
            'event_date': upload['event_date'], // <-- Add this if it exists
            'description': upload['description'],
            'location': upload['location'],
            'pictures': uploadPictures, // <-- Pass the full list
            'links': uploadLinks,
            'pdfs': uploadPdfs, // <-- Add PDFs to the achievement data
            'first_picture': uploadPictures.isNotEmpty ? uploadPictures[0] : null,
          };
        }).toList();

        print('Found ${filteredAchievements.length} achievements'); // Debug print
        
        // Sort achievements by upload date (newest first)
        filteredAchievements.sort((a, b) {
          DateTime dateA = DateTime.parse(a['upload_date']);
          DateTime dateB = DateTime.parse(b['upload_date']);
          return dateB.compareTo(dateA); // Descending order (newest first)
        });

        // Add detailed logging for each achievement
        for (var i = 0; i < filteredAchievements.length; i++) {
          final achievement = filteredAchievements[i];
          print('Achievement $i:');
          print('  ID: ${achievement['id']}');
          print('  Title: ${achievement['title']}');
          print('  Has first picture: ${achievement['first_picture'] != null}');
          if (achievement['first_picture'] != null) {
            print('  Picture path: ${achievement['first_picture']['picture_path']}');
          }
        }

        setState(() {
          achievements = List<Map<String, dynamic>>.from(filteredAchievements);
          isLoading = false;
        });
      }
    } catch (e) {
      print('Error fetching achievements: $e');
      setState(() {
        isLoading = false;
      });
    }
  }

  Future<Uint8List?> _loadImage(String url) async {
    try {
      final response = await http.get(
        Uri.parse(url),
        headers: {
          'Connection': 'keep-alive',
          'Keep-Alive': 'timeout=5, max=1000',
          'Accept': '*/*',
          'Accept-Encoding': 'gzip, deflate',
        },
      ).timeout(
        const Duration(seconds: 30), // Increased timeout for larger images
        onTimeout: () {
          throw TimeoutException('The request timed out');
        },
      );

      if (response.statusCode == 200) {
        // If the image is too large, we'll resize it
        if (response.bodyBytes.length > 100 * 1024) { // 100KB
          print('Image is large (${response.bodyBytes.length ~/ 1024}KB), resizing...');
          // For now, just return the original bytes
          // TODO: Implement image resizing if needed
          return response.bodyBytes;
        }
        return response.bodyBytes;
      }
      return null;
    } catch (e) {
      print('Error in _loadImage: $e');
      return null;
    }
  }

  @override
  Widget build(BuildContext context) {
    return MainLayout(
      
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Achievements',
                  style: TextStyle(
                    fontSize: 24,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 20),
                if (isLoading)
                  const Center(child: CircularProgressIndicator())
                else if (achievements.isEmpty)
                  const Center(child: Text('No achievements found'))
                else
                  ListView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    padding: EdgeInsets.zero,
                    itemCount: achievements.length,
                    itemBuilder: (context, index) {
                      print('Building item at index $index of ${achievements.length}');
                      final achievement = achievements[index];
                      print('Achievement data at index $index: ${achievement['id']} - ${achievement['title']}');
                      
                      return Container(
                        margin: const EdgeInsets.only(bottom: 16),
                        child: Card(
                          elevation: 2,
                          child: InkWell(
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (context) => DisplaycontentPage(
                                    achievement: achievement,
                                    categoryName: 'Achievement',
                                    currentIndex: index,
                                    itemsList: achievements,
                                  ),
                                ),
                              );
                            },
                            child: Padding(
                              padding: const EdgeInsets.all(12.0),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  if (achievement['first_picture'] != null)
                                    ClipRRect(
                                      borderRadius: BorderRadius.circular(8),
                                      child: CachedNetworkImage(
                                        imageUrl: '${baseUrl}/storage/${achievement['first_picture']['picture_path']}',
                                        width: double.infinity,
                                        height: 180,
                                        fit: BoxFit.cover,
                                        memCacheWidth: 400,
                                        memCacheHeight: 400,
                                        maxWidthDiskCache: 400,
                                        maxHeightDiskCache: 400,
                                        placeholder: (context, url) => Container(
                                          width: double.infinity,
                                          height: 180,
                                          color: Colors.grey[200],
                                          child: const Center(
                                            child: CircularProgressIndicator(),
                                          ),
                                        ),
                                        errorWidget: (context, url, error) => Container(
                                          width: double.infinity,
                                          height: 180,
                                          color: Colors.grey[200],
                                          child: const Icon(Icons.error_outline, color: Colors.red),
                                        ),
                                      ),
                                    ),
                                  const SizedBox(height: 12),
                                  Text(
                                    achievement['title'],
                                    style: const TextStyle(
                                      fontSize: 18,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  const SizedBox(height: 8),
                                  Text(
                                    'Uploaded on: ${achievement['upload_date']}',
                                    style: TextStyle(
                                      color: Colors.grey[600],
                                      fontSize: 14,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      );
                    },
                  ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
