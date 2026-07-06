import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'dart:async';

class ImageSlider extends StatefulWidget {
  const ImageSlider({super.key});

  @override
  State<ImageSlider> createState() => _ImageSliderState();
}

class _ImageSliderState extends State<ImageSlider> {
  final PageController _pageController = PageController();
  List<Map<String, dynamic>> sliderPictures = [];
  bool isLoading = true;
  int currentPage = 0;
  Timer? _timer;
  Map<String, int> retryCounts = {}; // Track retry attempts for each image

  final String baseUrl = Platform.isAndroid 
      //? 'http://10.0.2.2:8000'     // Android Emulator
      ? 'http://192.168.202.164:8000'  // Physical Android device use pc ipaddress
      //? 'http://192.168.1.5:8000'  // Physical Android device use pc ipaddress
      : 'http://localhost:8000'; // iOS Simulator or other platforms    

  @override
  void initState() {
    super.initState();
    fetchSliderPictures();
    startAutoScroll();
  }

  @override
  void dispose() {
    _timer?.cancel();
    _pageController.dispose();
    super.dispose();
  }

  void startAutoScroll() {
    _timer = Timer.periodic(const Duration(seconds: 3), (timer) {
      if (sliderPictures.isNotEmpty) {
        if (currentPage < sliderPictures.length - 1) {
          currentPage++;
        } else {
          currentPage = 0;
        }
        _pageController.animateToPage(
          currentPage,
          duration: const Duration(milliseconds: 500),
          curve: Curves.easeInOut,
        );
      }
    });
  }

  Future<void> fetchSliderPictures() async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/api/slider_picture/Retrieve'));
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success']) {
          // Convert to list and sort by ID
          List<Map<String, dynamic>> pictures = List<Map<String, dynamic>>.from(data['data']);
          pictures.sort((a, b) {
            // Sort by ID (4,5,6,7,8)
            int idA = a['id'] ?? 0;
            int idB = b['id'] ?? 0;
            return idA.compareTo(idB);
          });

          setState(() {
            sliderPictures = pictures;
            isLoading = false;
          });
          
          // Log each picture path
            //   print('\n=== Slider Pictures (Sorted by ID) ===');
            //   for (var picture in sliderPictures) {
            //     print('ID: ${picture['id']}');
            //     print('Picture Path: ${picture['slider_picture_path']}');
            //     print('Full URL: $baseUrl/storage/${picture['slider_picture_path']}');
            //     print('Title: ${picture['slider_picture_title']}');
            //     print('-------------------');
            //   }
            //   print('=====================\n');
        }
      }
    } catch (e) {
      print('Error fetching slider pictures: $e');
      setState(() {
        isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    if (isLoading) {
      return const SizedBox(
        height: 250,
        child: Center(
          child: CircularProgressIndicator(),
        ),
      );
    }

    if (sliderPictures.isEmpty) {
      return const SizedBox(
        height: 250,
        child: Center(
          child: Text('No slider pictures available'),
        ),
      );
    }

    return SizedBox(
      height: 250,
      child: Stack(
        children: [
          PageView.builder(
            controller: _pageController,
            onPageChanged: (index) {
              setState(() {
                currentPage = index;
              });
            },
            itemCount: sliderPictures.length,
            itemBuilder: (context, index) {
              final picture = sliderPictures[index];
              return CachedNetworkImage(
                imageUrl: '$baseUrl/storage/${picture['slider_picture_path']}',
                fit: BoxFit.cover,
                memCacheWidth: 800,
                memCacheHeight: 400,
                maxWidthDiskCache: 800,
                maxHeightDiskCache: 400,
                placeholder: (context, url) => Container(
                  color: Colors.grey[200],
                  child: const Center(
                    child: CircularProgressIndicator(),
                  ),
                ),
                errorWidget: (context, url, error) {
                  final imageUrl = picture['slider_picture_path'];
                  retryCounts[imageUrl] = (retryCounts[imageUrl] ?? 0) + 1;

                  if (retryCounts[imageUrl]! <= 5) {
                    Future.delayed(const Duration(seconds: 1), () {
                      if (mounted) setState(() {});
                    });
                    return Container(
                      color: Colors.grey[200],
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const CircularProgressIndicator(),
                          const SizedBox(height: 4),
                          Text(
                            'Retrying... (${retryCounts[imageUrl]}/5)',
                            style: TextStyle(
                              color: Colors.blue[700],
                              fontSize: 12,
                            ),
                          ),
                        ],
                      ),
                    );
                  }

                  return Container(
                    color: Colors.grey[200],
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: const [
                        Icon(Icons.error_outline, color: Colors.red),
                        SizedBox(height: 4),
                        Text(
                          'Failed to load',
                          style: TextStyle(
                            color: Colors.blue,
                            fontSize: 12,
                          ),
                        ),
                      ],
                    ),
                  );
                },
              );
            },
          ),
          Positioned(
            bottom: 10,
            left: 0,
            right: 0,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: List.generate(
                sliderPictures.length,
                (index) => Container(
                  width: 8,
                  height: 8,
                  margin: const EdgeInsets.symmetric(horizontal: 4),
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: currentPage == index
                        ? Colors.white
                        : Colors.white.withOpacity(0.5),
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
} 