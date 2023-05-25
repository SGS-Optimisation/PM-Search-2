<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Collection
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string|null $name
 * @property string $search_mode
 * @property int $user_id
 * @property object|null $search_data
 * @property string|null $source_filepath
 * @property string|null $image_path
 * @property array|null $working_data
 * @property array|null $report
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon $consulted_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $search_data_is_ocr
 * @property-read mixed $search_data_is_phrase
 * @property-read mixed $search_data_kv
 * @property-read mixed $search_data_operator
 * @property-read mixed $search_data_search_string
 * @property-read mixed $search_data_type
 * @property-read mixed $title
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Collection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Collection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Collection onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Collection query()
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereConsultedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereReport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereSearchData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereSearchMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereSourceFilepath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereWorkingData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Collection withoutTrashed()
 */
	class Collection extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Search
 *
 * @property int $id
 * @property int $user_id
 * @property string $source_filepath
 * @property string $image_path
 * @property array $working_data
 * @property array $report
 * @property string $search_mode
 * @property int $created_at
 * @property int $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Search newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Search newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Search query()
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereReport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereWorkingData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereSourceFilepath($value)
 * @mixin \Eloquent
 * @property object|null $search_data
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereSearchData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereSearchMode($value)
 * @property int|null $parent_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Search> $children
 * @property-read int|null $children_count
 * @property-read mixed $search_data_is_ocr
 * @property-read mixed $search_data_is_phrase
 * @property-read mixed $search_data_kv
 * @property-read mixed $search_data_operator
 * @property-read mixed $search_data_search_string
 * @property-read mixed $search_data_type
 * @property-read mixed $title
 * @property-read Search|null $parent
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereParentId($value)
 */
	class Search extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string|null $azure_id
 * @property string $name
 * @property string|null $given_name
 * @property string|null $surname
 * @property string|null $job_title
 * @property string|null $office_location
 * @property string|null $mobile_phone
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $profile_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Search> $searches
 * @property-read int|null $searches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAzureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGivenName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereJobTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMobilePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOfficeLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

