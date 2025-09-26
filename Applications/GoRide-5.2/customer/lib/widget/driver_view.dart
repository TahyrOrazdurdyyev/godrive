import 'package:cached_network_image/cached_network_image.dart';
// Google Fonts replaced with local fonts
import 'package:customer/constant/constant.dart';
// Google Fonts replaced with local fonts
import 'package:customer/model/driver_user_model.dart';
// Google Fonts replaced with local fonts
import 'package:customer/themes/app_colors.dart';
// Google Fonts replaced with local fonts
import 'package:customer/utils/fire_store_utils.dart';
// Google Fonts replaced with local fonts
import 'package:flutter/material.dart';
// Google Fonts replaced with local fonts


class DriverView extends StatelessWidget {
  final String? driverId;

  const DriverView({super.key, this.driverId});

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<DriverUserModel?>(
        future: FireStoreUtils.getDriver(driverId.toString()),
        builder: (context, snapshot) {
          switch (snapshot.connectionState) {
            case ConnectionState.waiting:
              return const SizedBox();
            case ConnectionState.done:
              if (snapshot.hasError) {
                return Text(snapshot.error.toString());
              } else {
                if (snapshot.data == null) {
                  return Column(
                    children: [
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.center,
                        children: [
                          ClipRRect(
                            borderRadius: const BorderRadius.all(Radius.circular(10)),
                            child: CachedNetworkImage(
                              height: 50,
                              width: 50,
                              imageUrl: Constant.userPlaceHolder,
                              fit: BoxFit.cover,
                              placeholder: (context, url) => Constant.loader(),
                              errorWidget: (context, url, error) => Image.network(Constant.userPlaceHolder),
                            ),
                          ),
                          const SizedBox(
                            width: 10,
                          ),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text("Asynchronous user", style: TextStyle(fontWeight: FontWeight.w600)),
                                Row(
                                  children: [
                                    Expanded(
                                      child: Row(
                                        children: [
                                          const Icon(
                                            Icons.star,
                                            size: 22,
                                            color: AppColors.ratingColour,
                                          ),
                                          const SizedBox(
                                            width: 5,
                                          ),
                                          Text(Constant.calculateReview(reviewCount: "0.0", reviewSum: "0.0"), style: TextStyle(fontWeight: FontWeight.w500)),
                                        ],
                                      ),
                                    ),

                                  ],
                                )
                              ],
                            ),
                          ),
                        ],
                      ),
                    ],
                  );
                }
                DriverUserModel driverModel = snapshot.data!;
                return Column(
                  children: [
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.center,
                      children: [
                        ClipRRect(
                          borderRadius: const BorderRadius.all(Radius.circular(10)),
                          child: CachedNetworkImage(
                            height: 50,
                            width: 50,
                            imageUrl: driverModel.profilePic.toString(),
                            fit: BoxFit.cover,
                            placeholder: (context, url) => Constant.loader(),
                            errorWidget: (context, url, error) => Image.network(Constant.userPlaceHolder),
                          ),
                        ),
                        const SizedBox(
                          width: 10,
                        ),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(driverModel.fullName.toString(), style: TextStyle(fontWeight: FontWeight.w600)),
                              Row(
                                children: [
                                  Expanded(
                                    child: Row(
                                      children: [
                                        const Icon(
                                          Icons.star,
                                          size: 22,
                                          color: AppColors.ratingColour,
                                        ),
                                        const SizedBox(
                                          width: 5,
                                        ),
                                        Expanded(
                                          child: Text(Constant.calculateReview(reviewCount: driverModel.reviewsCount.toString(), reviewSum: driverModel.reviewsSum.toString()),
                                              style: TextStyle(fontWeight: FontWeight.w500)),
                                        ),
                                      ],
                                    ),
                                  ),
                                ],
                              )
                            ],
                          ),
                        ),
                      ],
                    ),
                  ],
                );
              }
            default:
              return const Text('Error');
          }
        });
  }
}
